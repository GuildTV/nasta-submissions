<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Entry\FileUpload;

class UploadRejectedFiles extends Mailable
{
    use Queueable, SerializesModels;

    private $upload;
    private $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FileUpload $upload, $files)
    {
        $this->upload = $upload;
        $this->files = [];

        foreach ($files as $file){
            $this->files[] = [
                'reason' => $file['reason'],
                'name' => $file['file']->name,
            ];
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->upload->station->email)
            ->subject("Uploaded files rejected")
            ->view('emails.station.entry.upload-rejected-files')
            ->with([
                'upload' => $this->upload,
                'files' => $this->getGroupedFiles(),
            ]);
    }

    private function getGroupedFiles(){
        $groupedFiles = [];

        foreach($this->files as $file){
            if (!array_key_exists($file['reason'], $groupedFiles))
                $groupedFiles[$file['reason']] = [];

            $groupedFiles[$file['reason']][] = $file['name'];
        }

        return $groupedFiles;
    }
}
