<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

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
                $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $folder->account->access_token);
                $dropbox = new Dropbox($client);

                $listFolderContents = $dropbox->listFolder($folder->folder_name);
                $files = $listFolderContents->getItems();

                Log::info("Found " . count($files) . " files");

                $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

                foreach ($files as $file){
                    $filename = $targetDir . $file->getName();
                    $file = $dropbox->move($folder->folder_name . "/" . $file->getName(), $filename);

                    $date = Carbon::parse($file->getServerModified());
                    $category = $this->parseCategoryName($file->getName());

                    UploadedFile::create([
                        'station_id' => $folder->station->id,
                        'account_id' => $folder->account->id,
                        'path' => $filename,
                        'name' => $file->getName(),
                        'category_id' => $category != null ? $category->id : null,
                        'late' => false, //$date->gte($category->closing_at), // TODO - is this needed??
                        'uploaded_at' => $date,
                    ]);

                    if ($category != null) {
                        UploadedFileLog::create([
                            'station_id' => $folder->station->id,
                            'category_id' => $category->id,
                            'level' => 'info',
                            'message' => 'File \'' . $file->getName() . '\' has been added',
                        ]);
                    }

                    Log::info("Imported: " . $file->getName());
                }

            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
            }
        }
    }

    private function parseCategoryName($name){
        if (!preg_match("/(.*)_(.*)_(.*)/", $name, $matches))
            return null;

        return Category::where('compact_name', $matches[2])->first();
    }
}
