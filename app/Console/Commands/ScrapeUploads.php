<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\UploadHelper;

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
        $helper = new UploadHelper();
        $helper->runForAll();

        // TODO - missing account info
    }
}
