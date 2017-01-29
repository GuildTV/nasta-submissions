<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Jobs\DropboxScrapeFolder;

use App\Database\Upload\StationFolder;

use Log;
use Exception;

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
    protected $description = 'Scrape uploads to dropbox folders';

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
        $folders = StationFolder::with('station')->with('account')->with('category')
            ->whereNotNull('last_accessed_at')->get();

        foreach($folders as $folder){
            // skip if can't be edited, or already submitted
            if ($folder->category != null && !$folder->category->canEditSubmissions() || $folder->category->getEntryForStation($folder->station->id)->submitted)
                continue;

            Log::info('Queueing scraping uploads for account: '.$folder->station->name . "(" . $folder->category_id . ")");

            try {
                $client = new DropboxFileServiceHelper($folder->account->access_token);
                dispatch(new DropboxScrapeFolder($folder, $client));

            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
            }
        }
    }

}
