<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\DropboxAccount;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;

use Config;
use Exception;
use Log;
use Mail;

class DropboxMigrateFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $newAccount;
    protected $oldprefix;
    protected $newprefix;
    protected $client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, DropboxAccount $newAccount, $oldprefix="/", $newprefix="/", $client=null)
    {
        $this->file = $file;
        $this->newAccount = $newAccount;
        $this->oldprefix = $oldprefix;
        $this->newprefix = $newprefix;
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = $this->client != null ? $this->client : new DropboxFileServiceHelper($this->newAccount->access_token);

        Log::info('Migrating file #' . $this->file->id . ' to account: '.$this->newAccount->id);

        if (strpos($this->file->path, $this->oldprefix) != 0){
            Log::warning("Failed to move file with non-matching prefix");
            return "BAD_PATH";
        }

        $newPath = substr_replace($this->file->path, $this->newprefix, 0, strlen($this->oldprefix));
        $newUrl = $client->getPublicUrl($newPath);
        if ($newUrl == null){
            Log::warning("Failed to get new url for file");
            return "FILE_NOT_EXIST";
        }
        $newUrl .= (parse_url($newUrl, PHP_URL_QUERY) ? '&' : '?') . 'raw=1';

        $oldPath = $this->file->path;
        $oldAccount = $this->file->account_id;

        $this->file->account_id = $this->newAccount->id;
        $this->file->path = $newPath;
        $this->file->public_url = $newUrl;
        $this->file->save();

        UploadedFileLog::create([
            'station_id' => $this->file->station_id,
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => 'info',
            'message' => 'Migrated file from ' . $oldAccount . ' to ' . $this->newAccount->id,
        ]);

        Log::warning("Migrated file #" . $this->file->id);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        UploadedFileLog::create([
            'station_id' => $this->file->station->id,
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => 'error',
            'message' => 'Failed to get migrate file \'' . $this->file['name'] . '\'',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Dropbox migrate file: File #" . $this->file->id);
    }
}
