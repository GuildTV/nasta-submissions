<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Category\Category;

use Log;
use Exception;
use Config;

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
        $folders = StationFolder::with('station')->with('account')->get();
        foreach($folders as $folder){
            Log::info('Scraping uploads for account: '.$folder->station->name);

            try {
                $client = new DropboxFileServiceHelper($folder->account->access_token);
                $this->scrapeFolder($client, $folder);
            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
                return $e;
            }

            return null;
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
            $parts = pathinfo($file['name']);

            $filename = $targetDir . $parts['filename'] . "_" . $file['rev'] . "." . $parts['extension'];
            $file = $client->move($folder->folder_name . "/" . $file['name'], $filename);
            if ($file == null) {
                Log::warning("Failed to move file between dropbox folders");
                continue;
            }

            $category = $this->parseCategoryName($file['name']);

            UploadedFile::create([
                'station_id' => $folder->station->id,
                'account_id' => $folder->account->id,
                'path' => $filename,
                'name' => $file['name'],
                'size' => $file['size'],
                'category_id' => $category != null ? $category->id : null,
                'uploaded_at' => $file['modified'],
            ]);

            if ($category != null) {
                UploadedFileLog::create([
                    'station_id' => $folder->station->id,
                    'category_id' => $category->id,
                    'level' => 'info',
                    'message' => 'File \'' . $file['name'] . '\' has been added',
                ]);
            }

            Log::info("Imported: " . $file['name']);
        }
    }

    private function parseCategoryName($name){
        if (!preg_match("/(.*)_(.*)_(.*)/U", $name, $matches))
            return null;

        $cats = Category::where('compact_name', $matches[2])->get();
        foreach ($cats as $cat){
            if ($cat->canEditSubmissions())
                return $cat;
        }

        return null;
    }
}
