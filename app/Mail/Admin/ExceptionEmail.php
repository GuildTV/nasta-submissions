<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Exceptions\InvalidArgumentException;

use Exception;
use Mail;

use Carbon\Carbon;

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler as Handler;

class ExceptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $exception;

    public static function notifyAdmin(Exception $exception, $subject=null)
    {
        $address = env("MAIL_ADMIN", "");
        if (strlen($address) > 0)
            Mail::to($address)->queue(new ExceptionEmail($exception, $subject));
    }

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Exception $exception, $subject=null)
    {
        $this->exception = $exception;
        $this->subject = $subject == null ? "NaSTA submissions exception!" : $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $body = $this->whoops()->handleException($this->exception);

        return $this->subject($this->subject)
            ->view('emails.admin.exception')
            ->with([
                'body' => $body,
            ]);
    }

    /**
     * Get the whoops instance.
     *
     * @return \Whoops\Run
     */
    private function whoops()
    {
        $whoops = new Whoops();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new Handler());
        return $whoops;
    }
}
