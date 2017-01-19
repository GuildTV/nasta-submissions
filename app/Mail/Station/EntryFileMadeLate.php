<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;

class EntryFileMadeLate extends Mailable
{
    use Queueable, SerializesModels;

    protected $category;
    protected $entry;
    protected $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Category $category, UploadedFile $file)
    {
        $this->category = $category;
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
        $entry = $this->category->getEntryForStation($this->file->station_id);

        return $this->subject('Your entry for the '.$this->category->name.' award is now late')
            ->view('emails.station.file-made-late')
            ->text('emails.station.file-made-late_plain')
            ->with([
                'file' => $this->file,
                'entry' => $entry,
                'category' => $this->category,
                'user' => $user,
            ]);
    }
}
