<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;
use App\Helpers\Files\DropboxFileServiceHelper;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Upload\VideoMetadata;

use Config;
use Exception;
use Log;

class DropboxScrapeMetadata implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $client;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, $client=null)
    {
        $this->file = $file;
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = $this->client != null ? $this->client : new DropboxFileServiceHelper($this->file->account->access_token);
        $metadata = $client->getMetadata($this->file->path);
        if ($metadata == null) {
            Log::error("Failed to get metadata for file #" . $this->file->id);
            return "API_FAIL";
        }

        $res = $this->parseInfo($metadata);
        if ($res === false) {
            return "NOT_VALID";
        }

        if ($res == null) {
            if ($this->attempts() < 5) {
                $this->release(120); // try again in 120s
                return "PARSE_FAIL";
            }
            return "PARSE_FAIL";
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
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => 'info',
            'message' => 'Scraped metadata for file \'' . $this->file->name . '\' (#' . $this->file->id . ')',
        ]);

        return $res;
    }

    private function parseInfo($metadata){
        if (isset($metadata['pending']))
            return null;

        if (!isset($metadata['metadata']))
            return null;

        $data = $metadata['metadata'];
        if ($data['.tag'] != "video")
            return null;

        if (!isset($data['dimensions']))
            return null;

        if (!isset($data['dimensions']['width']) || !isset($data['dimensions']['height']) || !isset($data['duration']))
            return null;

        return [
            'height' => intval($data['dimensions']['height']),
            'width' => intval($data['dimensions']['width']),
            'duration' => intval($data['duration'])
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
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => 'error',
            'message' => 'Failed to get metadata for file \'' . $this->file['name'] . '\' (#' . $this->file->id . ')',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Dropdox scrape metadata: File #" . $this->file->id);
    }
}
