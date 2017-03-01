<?php
namespace App\Jobs\Encode;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;

use App\Jobs\Encode\UploadEncoded;
use App\Jobs\DropboxScrapeMetadata;
use App\Jobs\OfflineRuleCheckFile;

use App\Helpers\Files\DropboxFileServiceHelper;

use Config;
use Exception;
use Log;

use Carbon\Carbon;

class UploadEncoded implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $srcFile;
    protected $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($srcFile, UploadedFile $file)
    {
        $this->srcFile = $srcFile;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting upload of new #' . $this->file->id);
        $client = new DropboxFileServiceHelper($this->file->account->access_token);

        $fullPath = Config::get('nasta.local_entries_path') . $this->srcFile;
        $srcFile = pathinfo($this->file->path);
        $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $this->file->station->name;
        $targetPath = $targetDir.'/'.$srcFile['filename'].'-manual'.'.'.@$srcFile['extension'];

        if (!file_exists($fullPath))
            throw new Exception("Source file does not exist");

        // upload new file
        $res = $client->upload($fullPath, $targetPath);
        if ($res == null)
            throw new Exception("Failed to upload new file #" . $this->file->id, 500);

        $url =  $client->getPublicUrl($targetPath);
        $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'raw=1';

        $file = UploadedFile::create([
            'station_id' => $this->file->station->id,
            'account_id' => $this->file->account->id,
            'path' => $targetPath,
            'path_local' => $this->srcFile,
            'name' => $this->file->name,
            'size' => $res['size'],
            'hash' => $res['hash'],
            'public_url' => $url,
            'category_id' => $this->file->category_id,
            'uploaded_at' => $this->file->uploaded_at,
        ]);

        // mark the entry as pending
        $entry = $this->getEntryForStation($this->file->station_id);
        if ($entry->rule_break != null)
            $entry->rule_break->result = "pending";

        // update old version
        $this->file->replacement_id = $file->id;
        $this->file->save();

        UploadedFileLog::create([
            'station_id' => $file->station->id,
            'uploaded_file_id' => $file->id,
            'category_id' => $file->category_id,
            'level' => 'info',
            'message' => 'File \'' . $res['name'] . '\' has been replaced with an transcoded copy',
        ]);

        try {
            dispatch((new OfflineRuleCheckFile($file))->onQueue('process'));
            dispatch((new DropboxScrapeMetadata($file))->delay(Carbon::now()->addMinutes(5)));
        } catch (Exception $e) {
            Log::warning("Dropbox upload processing for file failed. Ignoring.");
        }
    
        Log::info("Transcoded: " . $res['name']);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Failed to uplaod replacement file for #" . $this->file->id);
    }
}
