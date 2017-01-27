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

    private $body;
    private $subjectStr;

    public static function notifyAdmin(Exception $exception, $subjectStr=null)
    {
        $address = env("MAIL_ADMIN", "");
        if (strlen($address) > 0)
            Mail::to($address)->queue(new ExceptionEmail($exception, $subjectStr));
    }

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Exception $exception, $subjectStr=null)
    {
        $this->body = $this->whoops()->handleException($exception);
        $this->subjectStr = $subjectStr == null ? "NaSTA submissions exception!" : $subjectStr;

        if ($this->body == null || strlen($this->body) == 0)
            $this->body = $exception->getTraceAsString();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subjectStr)
            ->view('emails.admin.exception')
            ->with([
                'body' => $this->body ? $this->body : "No message",
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
