<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\User;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $token=null)
    {
        $this->user = $user;
        $this->token = $token == null ? "null" : $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password')
            ->view('emails.auth.password-reset')
            ->text('emails.auth.password-reset_plain')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
