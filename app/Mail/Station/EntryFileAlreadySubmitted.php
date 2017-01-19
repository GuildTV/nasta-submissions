<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;
use App\Database\Entry\Entry;

class EntryFileAlreadySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    protected $file;
    protected $category;
    protected $entry;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Entry $entry, UploadedFile $file)
    {
        $this->category = $entry->category;
        $this->file = $file;
        $this->entry = $entry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->file->station;

        return $this->subject('A file has been added to your entry for the '.$this->category->name.' award')
            ->view('emails.station.file-already-submitted')
            ->text('emails.station.file-already-submitted_plain')
            ->with([
                'file' => $this->file,
                'entry' => $this->entry,
                'category' => $this->category,
                'user' => $user,
            ]);
    }
}
