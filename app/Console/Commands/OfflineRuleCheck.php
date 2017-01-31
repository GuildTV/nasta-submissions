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
        $files = UploadedFile::query();

        $runAll = ($id == 0 || $id == null);
        if ($runAll) {
            $files = $files->doesntHave('rule_break');
        } else {
            $files = $files->where('id', $id);
        }

        $files = $files
            ->whereNotNull('category_id')
            ->whereNotNull('path_local')
            ->get();

        Log::info("Found " . $files->count() . " files to check");

        foreach ($files as $file){
            try {
                dispatch((new OfflineJob($file, !$runAll))->onQueue('downloads'));

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }
    }

}
