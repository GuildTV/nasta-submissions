<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Upload\DropboxAccount;

use Config;

class EnsureFilesExistEverywhereMail extends Mailable
{
    use Queueable, SerializesModels;

    private $finalFailures;
    private $threshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $finalFailures)
    {
        $this->finalFailures = $finalFailures;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("NaSTA Submissions - " . count($this->finalFailures) . ' files are missing')
            ->text('emails.admin.ensure-files-exist-everywhere')
            ->with([
                'finalFailures' => $this->finalFailures,
            ]);
    }
}
