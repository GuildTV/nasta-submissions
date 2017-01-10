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
        $this->entry = $category->getEntryForStation($file->station_id);

        if ($this->entry == null)
            throw new InvalidArgumentException("category->entry should not be null");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->file->station;

        return $this->subject('Your entry for the '.$this->category->name.' award is now late')
            ->view('emails.station.file-made-late')
            ->text('emails.station.file-made-late_plain')
            ->with([
                'file' => $this->file,
                'entry' => $this->entry,
                'category' => $this->category,
                'user' => $user,
            ]);
    }
}
