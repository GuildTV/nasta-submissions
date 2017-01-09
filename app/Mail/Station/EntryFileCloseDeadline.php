<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use App\Database\Upload\UploadedFile;

class EntryFileCloseDeadline extends Mailable
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

        if ($file->category == null)
            throw new InvalidArgumentException("file->category should not be null");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->file->station;
        $entry = $this->file->category != null ? $this->file->category->getEntryForStation($user->id) : null;

        return $this->subject('Your file \''.$this->file->name.'\' has been uploaded')
            ->view('emails.station.file-uploaded')
            ->text('emails.station.file-uploaded_plain')
            ->with([
                'file' => $this->file,
                'entry' => $entry,
                'user' => $user,
            ]);
    }
}
