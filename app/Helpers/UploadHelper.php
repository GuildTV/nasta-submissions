<?php 
namespace App\Helpers;

use App\Helpers\GoogleHelper;
use App\Database\Entry\FileUpload;

use Log;
use Exception;

use Google_Service_Drive_DriveFile;

class UploadHelper {
    private $missingAccounts = [];
    private $exceptions = [];

    public function getMissingAccounts(){
        return $this->missingAccounts;
    }

    public function getExceptions(){
        return $this->exceptions;
    }

    public function runForAll(){
        $uploads = FileUpload::whereNotNull('scratch_folder_id')
            ->with('constraint')->with('category')->with('station')
            ->get();

        foreach($uploads as $upload) {
            try {
                $this->runForUpload($upload);
            } catch (Exception $e){
                $this->exceptions[] = $e;
                Log::error("Got exception: " . $e->getMessage());
            }
        }
    }

    private function getClient($upload){
        if (in_array($upload->account_id, $this->missingAccounts)){
            return null;
        }

        $client = GoogleHelper::getDriveClient($upload->account_id);
        if ($client == null){
            $this->missingAccounts[] = $upload->account_id;
            return null;
        }

        return $client;
    }

    public function runForUpload($upload){
        Log::info('Scraping upload progress: '.$upload->id);

        $client = $this->getClient($upload);
        if ($client == null) {
            Log::warning("Skipping due to missing acount details");
            return;
        }

        $optParams = array(
          'pageSize' => 1000,
          'fields' => 'files(id,md5Checksum,mimeType,name,size,videoMediaMetadata),nextPageToken',
          'q' => "'" . $upload->scratch_folder_id . "' in parents and not appProperties has { key='ignore' and value='rule' }"
        );
        $results = $client->files->listFiles($optParams);
        $files = $results->getFiles();

        if (count($files) == 0) {
            Log::info("No files to process in folder");
            return;
        }

        Log::info("Found " . count($files) . " in folder");

        $deletedFiles = [];
        $matchedFiles = [];

        $mimetypes = $upload->constraint->mimetypes;
        $acceptedTypes = strlen($mimetypes) > 0 ? explode(";", $mimetypes) : [];
        if (count($acceptedTypes) == 0)
            Log::warning("No mime types defined for constraint #" . $upload->constraint->id . " skipping checks");

        foreach ($files as $file){ // TODO - skip rules pdf
            if (count($acceptedTypes) > 0 && !in_array($file->mimeType, $acceptedTypes)){
                $deletedFiles[] = [
                    'reason' => "BAD_TYPE",
                    'file' => $file
                ];
                continue;
            }

            // TODO - check md5 hash

            if ($upload->constraint->video_duration != null) { // File is a video
                $res = $this->validateVideoFile($file, $upload);
                if ($res === false)
                    continue;
                if ($res !== true)
                    $deletedFiles[] = $res;
            }

            $matchedFiles[] = $file;
        }

        // approve a matched file
        if (count($matchedFiles) == 0){
            Log::info("Found no valid files to use");

            // TODO this delete
            print "DELETING " . count($deletedFiles) . " FILES\n\n";

        } else {
            $this->finaliseUpload($client, $upload, $matchedFiles);
        }
    }

    private function finaliseUpload($client, $upload, $matchedFiles){
        Log::info("Found " . count($matchedFiles) . ". Choosing the first seen");
        $file = $matchedFiles[0];

        $target_dir = $this->getOrCreateTargetDir($client, $upload->account);

        $copyMetadata = new Google_Service_Drive_DriveFile([
          "name" => $upload->category->name . " - " . $upload->station->name . " - " . $upload->id . " - " . "TODO entry name", // TODO
          "parents" => [ $target_dir ],
          'appProperties' => [
            'ignore' => 'rules' // mark as ignore due to being the rules pdf
          ],
        ]);
        $newfile = $client->files->copy($file->id, $copyMetadata);

        // delete the source folder
        $client->files->delete($upload->scratch_folder_id);

        // mark upload as completed
        $upload->scratch_folder_id = null;
        $upload->final_file_id = $newfile->id;
        $upload->save();
        Log::info("Success");

        // send email
    }

    private function getOrCreateTargetDir($client, $account) {
        $dir = $account->target_dir;
        if ($dir != null && strlen($dir) > 0)
            return $dir;

        $fileMetadata = new Google_Service_Drive_DriveFile([
          'name' => "Completed Uploads",
          'mimeType' => 'application/vnd.google-apps.folder',
        ]);
        $file = $client->files->create($fileMetadata, ['fields' => 'id']);

        $account->target_dir = $file->id;
        $account->save();

        return $file->id;
    }

    private function validateVideoFile($file, $upload){
        if ($file->videoMediaMetadata == null) {
            Log::error("Failed to process file due to missing video metadata");
            return false;
        }

        $metadata = $file->videoMediaMetadata;

        // check file duration
        $duration = floor($metadata->durationMillis / 1000);
        if ($duration > $upload->constraint->video_duration){
            // TODO - raise to admins?
            Log::warning("Found video file that is too long");
            return [
                'reason' => "TOO_LONG",
                'file' => $file
            ];
        }

        // check resolution, and bitrate
        $err = $this->resolutionConform($metadata, $file->size);
        if ($err !== false){
            return [
                'reason' => $err,
                'file' => $file
            ];
        }
    }

    private function resolutionConform($metadata, $size){
        foreach(Config::get('nasta.video_specs') as $spec) {
            // Choose the first found resolution group
            if ($metadata->width != $spec['w'] && $metadata->height != $spec['h'])
                continue;

            $sizeMb = intval($size) / 1024 / 1024;
            $durationS = intval($metadata->durationMillis) / 1000;
            $estimatedBitrate = $sizeMb * 8 / $durationS;

            if ($estimatedBitrate <= $spec['bitrate'] * Config::get('nasta.video_bitrate_tolerance.acceptable')){
                return false;
            } else if ($estimatedBitrate <= $spec['bitrate'] * Config::get('nasta.video_bitrate_tolerance.needs_approval')){
                Log::warning("Found file with bitrate needing approval");
                // TODO - flag for approval
                return false;
            } else {
                Log::warning("Found file with too high bitrate");
                // TODO - raise to admins
                return "BAD_BITRATE";
            }
        }

        Log::warning("Found file with invalid resolution");
        return "BAD_RESOLUTION";
    }
}