<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\Encode\QueueEncode;

use App\Database\Upload\UploadedFile;

use Log;
use Exception;

class EncodeFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encode-file {id : The ID of the entry} {profile : Profile to encode to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a transcode of a submitted file';

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
        $profile = $this->argument('profile');

        $file = UploadedFile::find($id);
        if ($file == null)
            return Log::error("Failed to find file");

        dispatch((new QueueEncode($file, $profile))->onQueue('downloads'));
    }

}
