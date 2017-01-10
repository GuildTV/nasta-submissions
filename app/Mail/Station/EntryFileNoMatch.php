<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Upload\UploadedFile;

class EntryFileNoMatch extends Mailable
{
    use Queueable, SerializesModels;

    protected $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->file->station;

        return $this->subject('Unable to match your file \''.$this->file->name.'\' to an entry')
            ->view('emails.station.file-no-match')
            ->text('emails.station.file-no-match_plain')
            ->with([
                'file' => $this->file,
                'user' => $user,
            ]);
    }
}
