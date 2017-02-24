<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;
use App\Mail\Admin\EnsureFilesExistEverywhereMail;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\UploadedFile;

use Config;
use Exception;
use Mail;
use Log;

class EnsureFilesExistEverywhere implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $sendEmail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($sendEmail)
    {
        $this->sendEmail = $sendEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $files = UploadedFile::with('account')->take(1)->get();
        $failures = [];

        foreach ($files as $file){
            $issues = $this->calculateFileIssues($file);
            $name = $file->name . " (#" . $file->id . ")";

            Log::info("File issues: " . $name . " with " . count($issues));

            if (count($issues) > 0)
                $failures[] = $file;
        }

        $finalFailures = [];
        // check failed havent been deleted from db or had a name changed
        foreach ($failures as $file){
            $file2 = UploadedFile::find($file->id);
            if ($file2 == null)
                continue;

            $issues = $this->calculateFileIssues($file2);
            $name = $file->name . " (#" . $file->id . ")";

            Log::info("File issues: " . $name . " still with " . count($issues));

            if (count($issues) > 0)
                $finalFailures[$name] = $issues;
        }

        $address = env("MAIL_ADMIN", "");
        if ($this->sendEmail && strlen($address) > 0){
            Mail::to($address)->queue(new EnsureFilesExistEverywhereMail($finalFailures));
        }

        return $finalFailures;
    }

    private function calculateFileIssues(UploadedFile $file){
        $issues = [];

        // TODO - reuse/cache helpers and allow to be faked in unit tests
        $helper = new DropboxFileServiceHelper($file->account->access_token);

        if (!$this->fileIsLocal($file))
            $issues[] = 'NOT_LOCAL';

        if (!$helper->fileExists($file->path))
            $issues[] = 'NOT_REMOTE';

        if (!$this->publicUrlIsValid($file))
            $issues[] = 'NOT_PUBLIC';

        return $issues;
    }

    private function fileIsLocal(UploadedFile $file){
        if ($file->path_local == null)
            return false;

        $fullPath = Config::get('nasta.local_entries_path') . $file->path_local;
        if (!file_exists($fullPath))
            return false;

        return true;
    }

    private function publicUrlIsValid(UploadedFile $file){
        $http = curl_init($file->public_url);
        curl_setopt($http, CURLOPT_HEADER, true);
        curl_setopt($http, CURLOPT_NOBODY, true);
        curl_setopt($http, CURLOPT_FAILONERROR, true);
        curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'HEAD');
        $result = curl_exec($http);
        curl_close($http);
        return $result;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Failed to ensure files exist");
    }
}
