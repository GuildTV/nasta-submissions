<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\DropboxScrapeMetadata;

use App\Database\Upload\UploadedFile;

use Log;
use Exception;

class ScrapeDropboxVideoMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape-dropbox-metadata:file {id=0 : The ID of the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape dropbox video metadata for a file';

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
        $id = $this->argument('id');
        $files = UploadedFile::query();

        $runAll = ($id == 0 || $id == null);
        if ($runAll) {
            $files = $files->whereNull('video_metadata_id');
        } else {
            $files = $files->where('id', $id);
        }

        $files = $files->get();

        Log::info("Found " . $files->count() . " files to check");

        foreach ($files as $file){
            try {
                dispatch(new DropboxScrapeMetadata($file));

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }
    }

}
