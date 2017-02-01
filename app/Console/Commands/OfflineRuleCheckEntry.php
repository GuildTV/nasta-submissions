<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\OfflineRuleCheckEntry as OfflineJob;

use App\Database\Entry\Entry;

use Log;
use Exception;

class OfflineRuleCheckEntry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rule-check:entry {id=0 : The ID of the entry}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run an offline rule check for an entry';

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
        $entry = Entry::query();

        $runAll = ($id == 0 || $id == null);
        if ($runAll) {
            $entry = $entry->doesntHave('rule_break');
        } else {
            $entry = $entry
                ->where('id', $id)
                ->where('submitted', false);
        }

        $entries = $entry->get();

        Log::info("Found " . $entries->count() . " entries to check");

        foreach ($entries as $entry){
            try {
                dispatch((new OfflineJob($entry, !$runAll))->onQueue('downloads'));

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }
    }

}
