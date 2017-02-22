<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;
use App\Mail\Station\EntryFileNoMatch;
use App\Mail\Station\EntryFileCloseDeadline;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Upload\StationFolder;
use App\Database\Category\Category;

use Carbon\Carbon;

use Config;
use Exception;
use Log;
use Mail;

class DropboxScrapeFolder implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $folder;
    protected $client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(StationFolder $folder, $client=null)
    {
        $this->folder = $folder;
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = $this->client != null ? $this->client : new DropboxFileServiceHelper($this->folder->account->access_token);
        $folder = $this->folder;

        Log::info('Scraping uploads for account: '.$folder->station->name . "(" . $folder->category_id . ")");

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
            if ($category != null && (!$category->canEditSubmissions() || $category->getEntryForStation($folder->station->id)->submitted))
                $category = null;

            $categoryId = $category != null ? $category->id : null;

            $filename = $targetDir . $parts['filename'] . "_" . $file['hash'] . "." . @$parts['extension'];
            $file = $client->move($folder->folder_name . "/" . $file['name'], $filename);
            if ($file == null) {
                Log::warning("Failed to move file between dropbox folders");
                continue;
            }

            $url =  $client->getPublicUrl($filename);
            $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'raw=1';

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
                'uploaded_file_id' => $res->id,
                'category_id' => $categoryId,
                'level' => 'info',
                'message' => 'File \'' . $file['name'] . '\' has been added',
            ]);

            if ($url == null) {
                UploadedFileLog::create([
                    'station_id' => $folder->station->id,
                    'uploaded_file_id' => $res->id,
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

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        UploadedFileLog::create([
            'station_id' => $this->file->station->id,
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => 'error',
            'message' => 'Failed to get metadata for file \'' . $this->file['name'] . '\'',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Dropdox scrape metadata: File #" . $this->file->id);
    }
}
