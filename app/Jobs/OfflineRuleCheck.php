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

use Mhor\MediaInfo\MediaInfo;

use Config;
use Exception;
use Log;

class OfflineRuleCheck implements ShouldQueue
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
        // If not downloaded, nothing to do!
        if ($this->file->path_local == null) {    
            Log::warning('Skipping mediainfo of #' . $this->file->id . ', as db does not have it marked local');
            return false;
        }

        // TODO - check if data has already been generated

        $fullPath = Config::get('nasta.local_entries_path') . $this->file->path_local;
        if (!file_exists($fullPath))
            throw new Exception("Local file does not exist!");

        $mediaInfo = new MediaInfo();
        $mediaInfoContainer = $mediaInfo->getInfo($fullPath);
        $general = $mediaInfoContainer->getGeneral();

        if ($general == null) {
            // TODO - file is not a video
            return true;
        }
        
        if ($general->get('count_of_video_streams') != 1)
            $this->log("warn", "Invalid number of video streams " . $general->get('count_of_video_streams'));
        if ($general->get('count_of_audio_streams') != 1)
            $this->log("warn", "Invalid number of audio streams " . $general->get('count_of_audio_streams'));

        $metadata = [
            'audio' => [],
            'video' => [],
        ];

        $wrapper = $general->get('internet_media_type');
        if ($wrapper != null)
            $metadata['wrapper'] = $wrapper;

        $duration = $general->get('duration');
        if ($duration != null)
            $metadata['duration'] = $duration->getMilliseconds()/1000;

        $audios = $mediaInfoContainer->getAudios();
        if (count($audios) > 0){
            $audio = $audios[0];

            $format = $audio->get('format');
            if ($format != null)
                $metadata['audio']['format'] = $format->getShortName();

            $bitrate = $audio->get('bit_rate');
            if ($bitrate != null)
                $metadata['audio']['bit_rate'] = $bitrate->getAbsoluteValue()/1000;

            $maxbitrate = $audio->get('maximum_bit_rate');
            if ($maxbitrate != null)
                $metadata['audio']['maximum_bit_rate'] = $maxbitrate->getAbsoluteValue()/1000;

            $channels = $audio->get('channel_s');
            if ($channels != null)
                $metadata['audio']['channels'] = $channels->getAbsoluteValue();

            $sampling_rate = $audio->get('sampling_rate');
            if ($sampling_rate != null)
                $metadata['audio']['sampling_rate'] = $sampling_rate->getAbsoluteValue();
        }

        $videos = $mediaInfoContainer->getVideos();
        if (count($videos) > 0){
            $video = $videos[0];

            $format = $video->get('format');
            if ($format != null)
                $metadata['video']['format'] = $format->getShortName();

            $bitrate = $video->get('bit_rate');
            if ($bitrate != null)
                $metadata['video']['bit_rate'] = $bitrate->getAbsoluteValue()/1000;

            $bit_rate_mode = $video->get('bit_rate_mode');
            if ($bit_rate_mode != null)
                $metadata['video']['bit_rate_mode'] = $bit_rate_mode->getShortName();

            $maxbitrate = $video->get('maximum_bit_rate');
            if ($maxbitrate != null)
                $metadata['video']['maximum_bit_rate'] = $maxbitrate->getAbsoluteValue()/1000;

            $format_profile = $video->get('format_profile');
            if ($format_profile != null)
                $metadata['video']['format_profile'] = $format_profile;

            $width = $video->get('width');
            if ($width != null)
                $metadata['video']['width'] = $width->getAbsoluteValue();

            $height = $video->get('height');
            if ($height != null)
                $metadata['video']['height'] = $height->getAbsoluteValue();

            $pixel_aspect_ratio = $video->get('pixel_aspect_ratio');
            if ($pixel_aspect_ratio != null)
                $metadata['video']['pixel_aspect_ratio'] = floatval($pixel_aspect_ratio);

            $frame_rate = $video->get('frame_rate');
            if ($frame_rate != null)
                $metadata['video']['frame_rate'] = $frame_rate->getAbsoluteValue();

            $scan_type = $video->get('scan_type');
            if ($scan_type != null)
                $metadata['video']['scan_type'] = $scan_type->getShortName();

            $standard = $video->get('standard');
            if ($standard != null)
                $metadata['video']['standard'] = $standard;



        }



        // $metadata['audio']['duration'] = $audio->get('source_duration')[0]/1000;
        dd($metadata);


    }

    private function log($level, $message){
        UploadedFileLog::create([
            'station_id' => $this->file->station->id,
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => $level,
            'message' => $message . ' (#' . $this->file->id . ')',
        ]);
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
            'message' => 'Failed to scrape mediainfo for file \'' . $this->file['name'] . '\' (#' . $this->file->id . ')',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Failed mediainfo: File #" . $this->file->id);
    }
}
