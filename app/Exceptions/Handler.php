<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use GrahamCampbell\Exceptions\NewExceptionHandler;

use App\Mail\Admin\ExceptionEmail;

use Auth;
use Log;

class Handler extends NewExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        // email exception report
        if ($this->shouldReport($e)) {
            try {
                $user = Auth::user();
                $subject = "NaSTA Exception for " . ($user == null ? "Anonymous" : $user->name);
                ExceptionEmail::notifyAdmin($e, $subject);
            } catch (Exception $e){
                Log::error("Failed to send exception email: ", $e);
            }
        }
        
        parent::report($e);
    }

    public function render($request, Exception $exception)
    {
       if ($exception instanceof AuthenticationException) {
           return $this->unauthenticated($request, $exception);
       }

       return parent::render($request, $exception);
    }

}