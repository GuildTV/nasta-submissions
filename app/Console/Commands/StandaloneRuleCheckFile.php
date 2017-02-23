<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\OfflineRuleCheckFile as OfflineJob;

use App\Database\Upload\UploadedFile;

use Mhor\MediaInfo\MediaInfo;

use Log;
use Exception;

class StandaloneRuleCheckFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'standalone-check {path : The path to the file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a standalone rule check for a file';

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
        $path = $this->argument('path');
        if (!file_exists($path))
            throw new Exception("Bad path");

        $mediaInfo = new MediaInfo();
        $mediaInfoContainer = $mediaInfo->getInfo($path);

        $job = new OfflineJob(new UploadedFile, false);
        
        $metadata = $job->parseMetadata($mediaInfoContainer, false);
        if ($metadata == null)
            throw new Exception("Failed to parse metadata");

        $specs = $job->chooseSpecs($metadata, false);
        if ($specs == null)
            throw new Exception("Failed to choose specs");

        $res = $job->checkVideoConfirms($specs, $metadata);
        var_dump($res);
    }

}
