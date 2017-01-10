<?php

namespace App\Mail\Station;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Entry\Entry;

class EntrySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    protected $entry;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->entry->station;

        return $this->subject('Your entry for the '.$this->entry->category->name.' award')
            ->view('emails.station.entry-submitted')
            ->text('emails.station.entry-submitted_plain')
            ->with([
                'entry' => $this->entry,
                'user' => $user,
            ]);
    }
}
