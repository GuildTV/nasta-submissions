<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\GoogleHelper;
use App\Database\Upload\GoogleAccount;
use App\Database\Upload\DropboxAccount;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use Log;
use Exception;

class ScrapeFreeSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:free-space';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape free space for defined accounts';

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
        $accounts = GoogleAccount::all();
        foreach ($accounts as $account){
            Log::info('Scraping free space for drive: '.$account->id);

            try {
                $client = GoogleHelper::getDriveClient($account->id);
                if ($client == null){
                    Log::error('Failed to get client');
                    continue;
                }

                $about = $client->about->get([
                    'fields' => 'storageQuota'
                ]);

                if ($about->storageQuota == null)
                    throw new Exception("Failed to access storageQuota");

                $account->total_space = $about->storageQuota->limit;
                $account->used_space = $about->storageQuota->usage;
                $account->save();

            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
            }
        }

        $accounts = DropboxAccount::all();
        foreach ($accounts as $account){
            Log::info('Scraping free space for dropbox: '.$account->id);


            $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $account->access_token);
            $dropbox = new Dropbox($client);

            try {
              $accountSpace = $dropbox->getSpaceUsage();

              $account->used_space = $accountSpace['used'];
              $account->total_space = $accountSpace['allocation']['allocated'];
              $account->save();
            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
            }

        }
    }
}
