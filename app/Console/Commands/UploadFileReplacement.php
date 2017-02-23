<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Jobs\Encode\UploadEncoded;

use App\Database\Upload\UploadedFile;

use Log;
use Exception;

class UploadFileReplacement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-file:replace {id : The ID of the file} {path : The path of the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload a replacement file';

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
        $path = $this->argument('path');

        $file = UploadedFile::find($id);
        if ($file == null)
            throw new Exception("Bad file id!");

       dispatch((new UploadEncoded($path, $file))->onQueue('uploads'));
    }

}
