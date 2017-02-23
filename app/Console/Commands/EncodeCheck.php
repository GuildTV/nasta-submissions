<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Jobs\Encode\ScrapeWatch;

use Log;
use Exception;

class EncodeCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encode-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for finished encode jobs';

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
       dispatch((new ScrapeWatch())->onQueue('process'));
    }

}
