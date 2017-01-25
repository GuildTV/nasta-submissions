<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Jobs\DropboxDownloadFile;
use App\Jobs\DropboxScrapeMetadata;

use App\Mail\Station\EntryFileNoMatch;
use App\Mail\Station\EntryFileCloseDeadline;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Category\Category;

use Log;
use Exception;
use Config;
use Mail;

class ScrapeUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $folders = StationFolder::with('station')->with('account')->with('category')->get();
        foreach($folders as $folder){
            Log::info('Scraping uploads for account: '.$folder->station->name . "(" . $folder->category_id . ")");

            try {
                $client = new DropboxFileServiceHelper($folder->account->access_token);
                $this->scrapeFolder($client, $folder);
            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
            }
        }
    }

    public function scrapeFolder($client, $folder){
        $files = $client->listFolder($folder->folder_name);
        if ($files == null){
            Log::warning("Failed to list files in folder");
            return "FAILED_LIST";
        }

        Log::info("Found " . count($files) . " files");

        $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

        foreach ($files as $file){
            $rawName = $this->stripSubmitterName($file['name']);
            $parts = pathinfo($rawName);

            $category = $folder->category;
            if ($category == null) {
                $category = $this->parseCategoryName($rawName);
            }

            // Don't add to category which cant be edited
            if (!$category->canEditSubmissions() || $category->getEntryForStation($folder->station->id)->submitted)
                $category = null;

            $categoryId = $category != null ? $category->id : null;

            $filename = $targetDir . $parts['filename'] . "_" . $file['hash'] . "." . $parts['extension'];
            $file = $client->move($folder->folder_name . "/" . $file['name'], $filename);
            if ($file == null) {
                Log::warning("Failed to move file between dropbox folders");
                continue;
            }

            $url =  $client->getPublicUrl($filename);

            // $count = $this->countReasonsLate($folder->station, $category);

            $res = UploadedFile::create([
                'station_id' => $folder->station->id,
                'account_id' => $folder->account->id,
                'path' => $filename,
                'name' => $rawName,
                'size' => $file['size'],
                'hash' => $file['hash'],
                'public_url' => $url,
                'category_id' => $categoryId,
                'uploaded_at' => $file['modified'],
            ]);

            // $isNowLate = $count == 0 && $this->countReasonsLate($folder->station, $category) > 0;

            UploadedFileLog::create([
                'station_id' => $folder->station->id,
                'category_id' => $categoryId,
                'level' => 'info',
                'message' => 'File \'' . $file['name'] . '\' has been added',
            ]);

            if ($url == null) {
                UploadedFileLog::create([
                    'station_id' => $folder->station->id,
                    'category_id' => $categoryId,
                    'level' => 'error',
                    'message' => 'Missing public url for file \'' . $file['name'] . '\'',
                ]);
            }

            try {
                dispatch((new DropboxScrapeMetadata($res))->delay(Carbon::now()->addMinutes(5)));
                dispatch((new DropboxDownloadFile($res))->onQueue('downloads'));
            } catch (Exception $e) {
                Log::warning("Dropbox download for file failed. Ignoring.");
            }

            if ($category == null){
                // Notify wasnt matched
                Mail::to($folder->station)->queue(new EntryFileNoMatch($res));

            } else if ($category->isCloseToDeadline()) {
                // Notify file was accepted
                Mail::to($folder->station)->queue(new EntryFileCloseDeadline($res));

            } else {
                // Unexpected state.
            }

            Log::info("Imported: " . $file['name']);
        }
    }

    private function parseCategoryName($name){
        if (!preg_match("/^(.*)_(.*)_/U", $name, $matches))
            return null;

        $cats = Category::where('compact_name', $matches[2])->get();
        foreach ($cats as $cat){
            if ($cat->canEditSubmissions())
                return $cat;
        }

        return null;
    }

    private function stripSubmitterName($name){
        if (!preg_match("/^(.*) - (.*?)$/U", $name, $matches))
            return $name;

        return $matches[2];
    }

    private function countReasonsLate($user, $cat){
        if ($cat == null)
            return 0;

        $entry = $cat->getEntryForStation($user->id);
        return $entry->countReasonsLate();
    }
}
