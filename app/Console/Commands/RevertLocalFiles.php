<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Upload\UploadedFile;
use App\Database\Entry\Entry;

use Log;
use Exception;
use Config;
use Storage;

class RevertLocalFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revert-local-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revert local files to the original structure';

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
            $pathinfo = pathinfo($file->path);
            if (!isset($pathinfo['filename'])){
                print "Skipped #" . $file->id . "\n";
                continue;
            }

            $catId = $file->category != null ? $file->category->compact_name : "Pending";
            $expectedPath = $catId . "/" . $pathinfo['filename'];

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
