<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\Admin\ExceptionEmail;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;
use App\Database\Upload\UploadedFileRuleBreak;

use Mhor\MediaInfo\MediaInfo;

use Config;
use Exception;
use Log;

class OfflineRuleCheckFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $overwrite;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file, $overwrite=false)
    {
        $this->file = $file;
        $this->overwrite = $overwrite;
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
            return "NO_LOCAL";
        }

        if ($this->file->category_id == null){
            Log::warning('Skipping mediainfo of #' . $this->file->id . ', as it does not have a category');
            return "NO_CATEGORY";
        }

        // check if data has already been generated
        if (!$this->overwrite && $this->file->rule_break != null){
            Log::info("Already has rule check result for #" . $this->file->id);
            return "EXISTING";
        }

        $fullPath = Config::get('nasta.local_entries_path') . $this->file->path_local;
        if (!file_exists($fullPath))
            throw new Exception("Local file does not exist!");

        $mediaInfo = new MediaInfo();
        $mediaInfoContainer = $mediaInfo->getInfo($fullPath);
        
        $metadata = $this->parseMetadata($mediaInfoContainer);
        if ($metadata == null) {
            $mime = mime_content_type($fullPath);
            $length = $this->getFileLength($mime, $fullPath);

            $this->save([
                'uploaded_file_id' => $this->file->id,
                'result' => $length > 0 ? 'ok' : 'break',
                'mimetype' => $mime,
                'length' => $length,
                'metadata' => json_encode([]),
                'warnings' => json_encode([]), 
                'errors' => json_encode($length > 0 ? [] : [ "bad_mimetype" ]),
            ]);
            return "NON_VIDEO";
        }

        $specs = $this->chooseSpecs($metadata);
        if ($specs == null) {
            $resolution = $metadata['video']['width'] . "x" . $metadata['video']['height'];
            $this->log("error", "Failed to find specs with resolution " . $resolution);

            $this->save([
                'uploaded_file_id' => $this->file->id,
                'result' => 'break',
                'mimetype' => $metadata['wrapper'],
                'length' => $metadata['duration'],
                'metadata' => json_encode($metadata),
                'warnings' => json_encode([]), 
                'errors' => json_encode([ 'resolution' ]),
            ]);
            return "NO_SPEC";
        }
        
        $res = $this->checkVideoConfirms($specs, $metadata);
        $failures = $res['errors'];
        $warnings = $res['warnings'];

        $result = "ok";

        if (count($warnings) > 0){
            $this->log("warn", "File missing required check data: " . implode(", ", $warnings));
            $result = "warning";
        }

        if (count($failures) > 0){
            $this->log("error", "File failed conform checks with issues: " . implode(", ", $failures));
            $result = "break";
        } else {
            $this->log("info", "File passed conform checks");
        }

        $this->save([
            'uploaded_file_id' => $this->file->id,
            'result' => $result,
            'mimetype' => $metadata['wrapper'],
            'length' => $metadata['duration'],
            'metadata' => json_encode($metadata),
            'warnings' => json_encode($warnings), 
            'errors' => json_encode($failures),
        ]);
        return "OK";
    }

    private function getFileLength($mime, $fullPath){
        switch($mime){
            case "application/pdf":
                return str_word_count(\Spatie\PdfToText\Pdf::getText($fullPath));
            case "text/plain":
                return str_word_count(file_get_contents($fullPath));
        }

        return -1;
    }

    private function save($data){
        if ($this->file->rule_break != null)
            $this->file->rule_break->delete();

        return UploadedFileRuleBreak::create($data);
    }

    private function checkVideoConfirms($specs, $metadata){
        $errors = [];
        $warnings = [];

        if (!isset($metadata['wrapper']))
            $warnings[] = 'wrapper';
        else if ($specs['wrapper'] != $metadata['wrapper'])
            $errors[] = 'wrapper';

        // VIDEO 
        if (!isset($metadata['video']['format']))
            $warnings[] = 'video.format';
        else if (!in_array(strtoupper($metadata['video']['format']), $specs['video']['format']))
            $errors[] = 'video.format';

        if (!isset($metadata['video']['bit_rate_mode']))
            $warnings[] = 'video.bit_rate_mode';
        else if (strtoupper($metadata['video']['bit_rate_mode']) != strtoupper($specs['video']['bit_rate_mode']))
            $errors[] = 'video.bit_rate_mode';

        if (!isset($metadata['video']['format_profile']))
            $warnings[] = 'video.format_profile';
        else if (strtoupper($metadata['video']['format_profile']) != strtoupper($specs['video']['format_profile']))
            $errors[] = 'video.format_profile';

        if (!isset($metadata['video']['pixel_aspect_ratio']))
            $warnings[] = 'video.pixel_aspect_ratio';
        else if ($metadata['video']['pixel_aspect_ratio'] != $specs['video']['pixel_aspect_ratio'])
            $errors[] = 'video.pixel_aspect_ratio';

        if (!isset($metadata['video']['frame_rate']))
            $warnings[] = 'video.frame_rate';
        else if ($metadata['video']['frame_rate'] != $specs['video']['frame_rate'])
            $errors[] = 'video.frame_rate';

        if (!isset($metadata['video']['scan_type']))
            $warnings[] = 'video.scan_type';
        else if (strtoupper($metadata['video']['scan_type']) != strtoupper($specs['video']['scan_type']))
            $errors[] = 'video.scan_type';

        if (!isset($metadata['video']['standard']))
            $warnings[] = 'video.standard';
        else if (strtoupper($metadata['video']['standard']) != strtoupper($specs['video']['standard']))
            $errors[] = 'video.standard';

        if (!isset($metadata['video']['width']))
            $warnings[] = 'video.width';
        else if ($metadata['video']['width'] != $specs['video']['width'])
            $errors[] = 'video.width';

        if (!isset($metadata['video']['height']))
            $warnings[] = 'video.height';
        else if ($metadata['video']['height'] != $specs['video']['height'])
            $errors[] = 'video.height';

        if (!isset($metadata['video']['bit_rate']))
            $warnings[] = 'video.bit_rate';
        else if ($metadata['video']['bit_rate'] > $specs['video']['bit_rate'] * $specs['video']['bit_rate_tolerance'])
            $errors[] = 'video.bit_rate';

        if (!isset($metadata['video']['maximum_bit_rate']))
            $warnings[] = 'video.maximum_bit_rate';
        else if ($metadata['video']['maximum_bit_rate'] > $specs['video']['maximum_bit_rate'] * $specs['video']['maximum_bit_rate_tolerance'])
            $errors[] = 'video.maximum_bit_rate';

        // AUDIO
        if (!isset($metadata['audio']['format']))
            $warnings[] = 'audio.format';
        else if (!in_array(strtoupper($metadata['audio']['format']), $specs['audio']['format']))
            $errors[] = 'audio.format';

        if (!isset($metadata['audio']['channels']))
            $warnings[] = 'audio.channels';
        else if ($metadata['audio']['channels'] != $specs['audio']['channels'])
            $errors[] = 'audio.channels';

        if (!isset($metadata['audio']['sampling_rate']))
            $warnings[] = 'audio.sampling_rate';
        else if (!in_array($metadata['audio']['sampling_rate'], $specs['audio']['sampling_rate']))
            $errors[] = 'audio.sampling_rate';

        if (!isset($metadata['audio']['bit_rate']))
            $warnings[] = 'audio.bit_rate';
        else if ($metadata['audio']['bit_rate'] > $specs['audio']['bit_rate'] * $specs['audio']['bit_rate_tolerance'])
            $errors[] = 'audio.bit_rate';

        if (!isset($metadata['audio']['maximum_bit_rate']))
            $warnings[] = 'audio.maximum_bit_rate';
        else if ($metadata['audio']['maximum_bit_rate'] > $specs['audio']['maximum_bit_rate'] * $specs['audio']['maximum_bit_rate_tolerance'])
            $errors[] = 'audio.maximum_bit_rate';

        return [
            "errors" => $errors, 
            "warnings" => $warnings,
        ];
    }

    private function chooseSpecs($metadata) {
        $options = Config::get('nasta.video_specs');
        foreach ($options as $key => $opt) {
            if ($opt['video']['height'] != $metadata['video']['height'])
                continue;

            if ($opt['video']['width'] != $metadata['video']['width'])
                continue;

            $this->log("info", "Matched spec " . $key);
            return array_merge_recursive(Config::get('nasta.video_specs_common'), $opt);
        }

        return null;
    }

    private function parseMetadata($mediaInfoContainer){
        $general = $mediaInfoContainer->getGeneral();
        if ($general == null) {
            return null;
        }
        
        if ($general->get('count_of_video_streams') != 1)
            $this->log("warn", "Invalid number of video streams " . $general->get('count_of_video_streams'));
        if ($general->get('count_of_audio_streams') != 1)
            $this->log("warn", "Invalid number of audio streams " . $general->get('count_of_audio_streams'));

        if ($general->get('count_of_video_streams') == 0 && $general->get('count_of_audio_streams') == 0)
            return null;

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

        return $metadata;
    }

    private function log($level, $message){
        switch ($level){
            case "error":
                Log::error($message);
                break;
            case "warn":
                Log::warning($message);
                break;
            default:
                Log::info($message);
                break;
        }

        UploadedFileLog::create([
            'station_id' => $this->file->station->id,
            'uploaded_file_id' => $this->file->id,
            'category_id' => $this->file->category_id,
            'level' => $level,
            'message' => $message,
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
            'message' => 'Failed to update mediainfo for file \'' . $this->file['name'] . '\'',
        ]);

        ExceptionEmail::notifyAdmin($exception, "Failed mediainfo: File #" . $this->file->id);
    }
}
