<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view.name');
    }
}
