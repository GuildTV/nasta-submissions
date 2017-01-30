<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\OfflineRuleCheck as OfflineJob;

use App\Database\Upload\UploadedFile;

use Log;
use Exception;

class OfflineRuleCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:rule-check {id=0 : The ID of the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run an offline rule check for a file';

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
        $files = null;

        if ($id == 0 || $id == null) {
            $files = UploadedFile::all(); // TODO - filter out those with data
        } else {
            $files = UploadedFile::where('id', $id)->get();
        }

        Log::info("Found " . $files->count() . " files to check");

        foreach ($files as $file){
            try {
                dispatch((new OfflineJob($file))->onQueue('downloads'));

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }
    }

}
