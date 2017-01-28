<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\Upload\DropboxAccount;

use Config;

class AccountSpaceThresholdCrossed extends Mailable
{
    use Queueable, SerializesModels;

    private $account;
    private $threshold;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DropboxAccount $account, $threshold=null)
    {
        $this->account = $account;
        $this->threshold = $threshold == null ? 45.94 : $threshold;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(($this->threshold < 10 ? "CRITICAL: " : "") . 'Threshold reached on a dropbox account')
            ->text('emails.admin.account-space-threshold')
            ->with([
                'account' => $this->account,
                'threshold' => $this->threshold,
            ]);
    }
}
