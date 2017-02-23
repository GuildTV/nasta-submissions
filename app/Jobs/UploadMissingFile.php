<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\User;
use App\Database\Category\Category;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Upload\DropboxAccount;

use App\Jobs\DropboxScrapeMetadata;
use App\Jobs\OfflineRuleCheckFile;

use App\Helpers\Files\DropboxFileServiceHelper;

use Config;
use Exception;
use Log;

use Carbon\Carbon;

class UploadMissingFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $srcFile;
    protected $category;
    protected $station;
    protected $helper;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($srcFile, Category $category, User $station, $helper = null)
    {
        $this->srcFile = $srcFile;
        $this->category = $category;
        $this->station = $station;
        $this->helper = $helper;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting upload of new file to ' . $this->category->name . ' for ' . $this->station->name);
        $account = DropboxAccount::inRandomOrder()->first();
        $client = $this->helper != null ? $this->helper : new DropboxFileServiceHelper($account->access_token);

        $fullPath = Config::get('nasta.local_entries_path') . $this->srcFile;
        $srcFile = pathinfo($this->srcFile);
        $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $this->station->name;
        $targetPath = $targetDir.'/'.$srcFile['filename'].'-manual'.'.'.@$srcFile['extension'];

        if (!file_exists($fullPath))
            throw new Exception("Source file does not exist");

        // upload new file
        $res = $client->upload($fullPath, $targetPath);
        if ($res == null)
            throw new Exception("Failed to upload new file " . $this->srcFile, 500);

        $url =  $client->getPublicUrl($targetPath);
        $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?') . 'raw=1';

        $file = UploadedFile::create([
            'station_id' => $this->station->id,
            'account_id' => $account->id,
            'path' => $targetPath,
            'path_local' => $this->srcFile,
            'name' => $res['name'],
            'size' => $res['size'],
            'hash' => $res['hash'],
            'public_url' => $url,
            'category_id' => $this->category->id,
            'uploaded_at' => $res['modified'],
        ]);

        UploadedFileLog::create([
            'station_id' => $this->station->id,
            'uploaded_file_id' => $file->id,
            'category_id' => $this->category->id,
            'level' => 'info',
            'message' => 'File \'' . $res['name'] . '\' has been added',
        ]);

        if ($url == null) {
            UploadedFileLog::create([
                'station_id' => $this->station->id,
                'uploaded_file_id' => $file->id,
                'category_id' => $this->category->id,
                'level' => 'error',
                'message' => 'Missing public url for file \'' . $res['name'] . '\'',
            ]);
        }

        try {
            dispatch((new DropboxScrapeMetadata($file))->delay(Carbon::now()->addMinutes(5)));
            dispatch((new OfflineRuleCheckFile($file))->onQueue('process'));
        } catch (Exception $e) {
            Log::warning("Dropbox download for file failed. Ignoring.");
        }

        Log::info("Imported: " . $res['name']);
        return $file;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        ExceptionEmail::notifyAdmin($exception, "Failed to upload new file " . $this->srcFile);
    }
}
