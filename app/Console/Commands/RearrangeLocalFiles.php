<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Upload\UploadedFile;
use App\Database\Entry\Entry;

use Log;
use Exception;
use Config;
use Storage;

class RearrangeLocalFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rearrange-local-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rearrange local files to take various states into account';

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
        $rootDir = Config::get('nasta.local_entries_path');
        $files = UploadedFile::whereNotNull("path_local")
            ->with('station')
            ->with('category')
            ->get();

        foreach ($files as $file){
            $entry = Entry::where('category_id', $file->category_id)->where('station_id', $file->station_id)->first();

            $catId = $file->category != null ? $file->category->compact_name : "Pending";
            $subDir = "Pending";
            if ($file->replacement_id != null)
                $subDir = "Old";
            else if ($entry == null || $entry->rule_break == null)
                $subDir = "Pending";
            else if ($file->rule_break == "ok" || $file->rule_break == "accepted")
                $SubDir = "OK";

            $safeName = $entry == null ? "" : preg_replace("/[^a-zA-Z0-9]/", '', ucwords($entry->name));
            $info = pathinfo($file->path_local);
            $filename = $file->station->compact_name . "_" . $file->category->compact_name . "_" . $safeName . "_" . $file->id . "." . @$info['extension'];
            $expectedPath = $catId . "/" . $subDir . "/" . $filename;

            // Nothing to do
            if ($expectedPath == $file->path_local){
                print "Skipped #" . $file->id . "\n";
                continue;
            }

            try {
                $baseDir = pathinfo(Config::get('nasta.local_entries_path') . $expectedPath)['dirname'];
                @mkdir($baseDir, 0775, true);
                rename(Config::get('nasta.local_entries_path') . $file->path_local, Config::get('nasta.local_entries_path') . $expectedPath);

                $file->path_local = $expectedPath;
                $file->save();
                print "Done #" . $file->id ."\n";
            } catch (Exception $e){
                print "Failed #" . $file->id . ": " . $e->getMessage()."\n";
            }
        }

        print "\nDONE\n\n";
    }

}
