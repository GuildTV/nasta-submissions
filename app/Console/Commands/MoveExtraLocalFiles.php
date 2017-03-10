<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Database\Upload\UploadedFile;
use App\Database\Entry\Entry;

use Log;
use Exception;
use Config;
use Storage;

class MoveExtraLocalFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move-extra-local-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move extra local files to indicate they are old/extra';

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
        $dirs = self::getSubDirs($rootDir);

        foreach ($dirs as $dir){
            if ($dir == "Old" || $dir == "Extra")
                continue;
            $folderPath = $rootDir."/".$dir;

            foreach (self::getFiles($folderPath) as $file){
                $count = UploadedFile::where('path_local', $dir."/".$file)->count();
                if ($count > 0){
                    print "Skip " . $dir."/".$file ."\n";
                    continue;
                }

                try {
                    @mkdir($folderPath."/Extra/", 0775, true);
                    rename($folderPath."/".$file, $folderPath."/Extra/".$file);
                    print "Done " . $dir."/".$file ."\n";
                } catch (Exception $e){
                    print "Failed " .  $dir."/".$file .": " . $e->getMessage() ."\n";
                }
            }

            foreach (self::getSubDirs($folderPath) as $inner){
                foreach (self::getFiles($folderPath) as $file){
                    $count = UploadedFile::where('path_local', $dir."/".$inner."/".$file)->count();
                    if ($count > 0){
                        print "Skip " . $dir."/".$inner."/".$file ."\n";
                        continue;
                    }

                    try {
                        @mkdir($folderPath."/Extra/", 0775, true);
                        rename($folderPath."/".$inner."/".$file, $folderPath."/Extra/".$file);
                        print "Done " . $dir."/".$inner."/".$file ."\n";
                    } catch (Exception $e){
                        print "Failed " .  $dir."/".$inner."/".$file .": " . $e->getMessage() ."\n";
                    }
                }
            }

        }

        print "DONE\n\n";
    }

    private static function getSubDirs($path){
        $dirs = array();

        // directory handle
        $dir = dir($path);

        while (false !== ($entry = $dir->read())) {
            if ($entry != '.' && $entry != '..') {
               if (is_dir($path . '/' .$entry)) {
                    $dirs[] = $entry; 
               }
            }
        }

        return $dirs;
    }

    private static function getFiles($path){
        $dirs = array();

        // directory handle
        $dir = dir($path);

        while (false !== ($entry = $dir->read())) {
            if ($entry != '.' && $entry != '..') {
               if (is_file($path . '/' .$entry)) {
                    $dirs[] = $entry; 
               }
            }
        }

        return $dirs;
    }


}
