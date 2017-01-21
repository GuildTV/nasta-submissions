<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;
use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\VideoMetadata;

use Config;
use Exception;
use Log;

class DropboxScrapeMetadata implements ShouldQueue
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
        $client = new DropboxFileServiceHelper($this->file->account->access_token);
        $metadata = $client->getMetadata($this->file->path);
        if ($metadata == null) {
            Log::error("Failed to get metadata for file #" . $this->file->id);
            return;
        }

        $res = $this->parseInfo($metadata);
        if ($res === false) {
            if ($this->attempts() < 5) {
                return $this->release(120); // try again in 120s
            }
            return null;
        }

        if ($this->file->video_metadata_id != null){
            VideoMetadata::where('id', $this->file->video_metadata_id)->update($res);
        } else {
            $data = VideoMetadata::create($res);
            $this->file->video_metadata_id = $data->id;
            $this->file->save();
        }

        UploadedFileLog::create([
            'station_id' => $this->file->station->id,
            'category_id' => $this->file->category_id,
            'level' => 'info',
            'message' => 'Scraped metadata for file \'' . $file['name'] . '\' (#' . $this->file->id . ')',
        ]);

        return true;
    }

    private function parseInfo($metadata){
        if (!isset($metadata['media_info']))
            return false;

        if (isset($metadata['media_info']['pending']))
            return null;

        if (!isset($metadata['media_info']['metadata']))
            return null;

        $data = $metadata['media_info']['metadata'];
        if (!isset($data['video']))
            return null;

        if (!isset($data['video']['dimensions']))
            return null;

        if (!isset($data['video']['dimensions']['width']) || !isset($data['video']['dimensions']['height']) || !isset($data['video']['duration']))
            return null;

        return [
            'height' => intval($data['video']['dimensions']['height']),
            'width' => intval($data['video']['dimensions']['width']),
            'duration' => intval($data['video']['duration'])
        ];
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
            'category_id' => $this->file->category_id,
            'level' => 'error',
            'message' => 'Failed to get metadata for file \'' . $file['name'] . '\' (#' . $this->file->id . ')',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Dropdox scrape metadata: File #" . $this->file->id);
    }
}
