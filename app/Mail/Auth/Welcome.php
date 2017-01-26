<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Database\User;

use Config;

class Welcome extends Mailable
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
        return $this->subject('Welcome to NaSTA Awards ' . Config::get('nasta.year') . ' Submissions')
            ->view('emails.auth.welcome')
            ->text('emails.auth.welcome_plain')
            ->with([
                'user' => $this->user,
                'token' => $this->token,
            ]);
    }
}
