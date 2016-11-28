<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Entry\FileUpload;

class UploadComplete extends Mailable
{
    use Queueable, SerializesModels;

    private $upload;
    private $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FileUpload $upload, $filename)
    {
        $this->upload = $upload;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->upload->station->email)
            ->subject("File upload accepted")
            ->view('emails.station.entry.upload-complete')
            ->with([
                'upload' => $this->upload,
                'filename' => $this->filename,
            ]);
    }
}
