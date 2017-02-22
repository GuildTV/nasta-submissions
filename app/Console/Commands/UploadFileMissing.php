<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Jobs\UploadMissingFile;

use App\Database\User;
use App\Database\Category\Category;

use Log;
use Exception;

class UploadFileMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload-file:missing {station : The ID of the station} {category : The id of the category} {path : The path of the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload a missing file';

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
        $sid = $this->argument('station');
        $cid = $this->argument('category');
        $path = $this->argument('path');

        $station = User::find($sid);
        if ($station == null || $station->type != "station")
            throw new Exception("Bad station id!");
        $Category = Category::find($cid);
        if ($Category == null)
            throw new Exception("Bad Category id!");

       dispatch((new UploadMissingFile($path, $Category, $station))->onQueue('downloads'));
    }

}
