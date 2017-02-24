<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

use App\Jobs\EnsureFilesExistEverywhere as Job;

use Log;
use Exception;
use Mail;
use DB;

class EnsureFilesExistEverywhere extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ensure-files-exist-everywhere {direct=0 : run directly}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all files exist on both local storage and dropbox. Queues as a job as needs to run on \'process\' queue worker';

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
        $job = new Job(true);
        if ($this->argument('direct') == 1)
            $job->handle();
        else
            dispatch($job->onQueue('process'));

        Log::info('Queued ensure-files-exist job');
    }

}
