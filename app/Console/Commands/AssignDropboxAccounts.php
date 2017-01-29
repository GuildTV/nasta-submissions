<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\GoogleHelper;
use App\Database\Upload\GoogleAccount;
use App\Database\Upload\DropboxAccount;
use App\Database\Category\Category;
use App\Database\User;
use App\Database\Upload\StationFolder;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Mail\Admin\ExceptionEmail;

use Log;
use Exception;

class AssignDropboxAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign-dropbox-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign dropbox accounts to stations';

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
        $accounts = DropboxAccount::where('enabled', true)->get();
        $i = 0;

        $stations = User::where('type', 'station')->whereNull('dropbox_account_id')->get();
        foreach ($stations as $station){
            Log::info("Assigning dropbox for " . $station->name);

            if ($accounts->count() == 0)
                return Log::warn("No possible accounts to assign to stations");

            $acc = $accounts[$i++];
            if ($i >= $accounts->count())
                $i = 0;

            $station->dropbox_account_id = $acc->id;
            $station->save();

            Log::info("Chose " . $acc->id);
        }
    }
}
