<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\GoogleHelper;
use App\Database\Upload\GoogleAccount;
use App\Database\Upload\DropboxAccount;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Mail\Admin\ExceptionEmail;
use App\Mail\Admin\AccountSpaceThresholdCrossed;

use Mail;
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

    private $thresholds = [
        80, 60, 50, 40, 30, 20, 10,
        9, 8, 7, 6, 5, 4, 3, 2, 1, 0,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = DropboxAccount::all();
        foreach ($accounts as $account){
            Log::info('Scraping free space for dropbox: '.$account->id);


            $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $account->access_token);
            $dropbox = new Dropbox($client);

            try {
              $accountSpace = $dropbox->getSpaceUsage();

              $percentBefore = ($account->total_space - $account->used_space) / $account->total_space * 100;

              $account->used_space = $accountSpace['used'];
              $account->total_space = $accountSpace['allocation']['allocated'];
              $account->save();

              $percentAfter = ($account->total_space - $account->used_space) / $account->total_space * 100;

              if ($this->crossedThreshold($percentBefore, $percentAfter))
                Mail::to(env("MAIL_ADMIN", ""))->queue(new AccountSpaceThresholdCrossed($account, $percentAfter));


              Log::info('Account changed to '. $percentAfter . '% free');

            } catch (Exception $e){
                Log::error('Failed to scrape: '. $e->getMessage());
                ExceptionEmail::notifyAdmin($e, "Dropbox free space scrape failure: #" . $account->id);
            }

        }
    }

    private function crossedThreshold($before, $after){
        foreach ($this->thresholds as $threshold){
            if ($before > $threshold && $after <= $threshold)
                return true;
        }

        return false;
    }
}
