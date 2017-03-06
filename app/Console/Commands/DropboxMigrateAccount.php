<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\DropboxMigrateFile;

use App\Database\Upload\DropboxAccount;
use App\Database\Upload\UploadedFile;

use Log;
use Exception;

class DropboxMigrateAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dropbox-migrate-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from one dropbox account to another';

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
        $source_id = $this->ask('source dropbox account');
        $dest_id = $this->ask('destination dropbox account');

        $src = DropboxAccount::find($source_id);
        if ($src == null)
            throw new Exception("Failed to find dropbox account " . $source_id);
        $dest = DropboxAccount::find($dest_id);
        if ($dest == null)
            throw new Exception("Failed to find dropbox account " . $dest_id);

        $oldPrefix = $this->ask('Old file path? (eg "/Imported/")');
        $newPrefix = $this->ask('New file path? (eg "/Imported - 2/")');


        $files = UploadedFile::where('account_id', $source_id)->get();
        foreach ($files as $file){
            try {
                dispatch((new DropboxMigrateFile($file, $dest, $oldPrefix, $newPrefix)));

            } catch (Exception $e){
                Log::error('Failed to run: '. $e->getMessage());
            }
        }        
    }

}
