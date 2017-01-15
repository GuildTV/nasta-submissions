<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;
use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\UploadedFile;

use Config;
use Exception;
use Log;

class DropboxDownloadFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // If already downloaded, nothing to do!
        if ($this->file->path_local != null) {    
            Log::warning('Skipping download of #' . $this->file->id . ', as db already has it marked local');
            return false;
        }

        Log::info('Starting download of #' . $this->file->id);
        $client = new DropboxFileServiceHelper($this->file->account->access_token);

        @mkdir(Config::get('nasta.local_entries_path') . $this->targetDir(), 0775, true);
        $target = $this->targetFilename();
        $fullTarget = Config::get('nasta.local_entries_path') . $target;

        try {
            $client->download($this->file->path, $fullTarget);
        } catch (Exception $e) {
            throw new Exception("Failed to download file #" . $this->file->id, 500, $e);
        }

        // check file looks valid
        try {
            $this->checkFileMatchesHashAndSize($fullTarget);
        } catch (Exception $e) {
            // delete file if it exists
            @unlink($target);

            throw $e;
        }

        // Log that file is downloaded
        $this->file->path_local = $target;
        $this->file->save();
        Log::info('Download complete');

        return true;
    }

    private function targetDir()
    {
        return $this->file->station->name . "/";
    }

    private function targetFilename()
    {
        return $this->targetDir() . $this->file->name;
    }

    private function checkFileMatchesHashAndSize($path)
    {
        if (!file_exists($path))
            throw new Exception("Fresh downloaded file does not exist!");

        if (filesize($path) != $this->file->size)
            throw new Exception("Downloaded file has wrong size");

        if ($this->file->hash != null && self::computeFileHash($path) != $this->file->hash)
            throw new Exception("Downloaded file has wrong hash");
    }

    public static function computeFileHash($path)
    {
        // The hash code generated from the file content. It can be computed as follows. Firstly, split the file into blocks of 4 MB (4096 bytes). The last block may be smaller than 4 MB. Secondly, compute the hash code of each block using SHA-256. Thirdly, concatenate the hash code of all blocks to form a single string. Fourthly, compute the hash code of the concatenated string using SHA-256. And lastly, output the hash code in hexadecimal format. This field is marked optional, but should always be available, except in the unlikely case where we decide to change the representation in the future. If we do so, we will provide advanced notice to developers with transition process in detail. This field is optional.
        
        $hashes = "";

        $fh = fopen($path, "rb");
        while (($chunk = fread($fh, 4096*1024)) !== false) {
            if ($chunk == "")
                break;

            $hashes .= hash("sha256", $chunk, true);
        }

        fclose($fh);
        return hash("sha256", $hashes);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Dropdox download failure: File #" . $this->file->id);
    }
}
