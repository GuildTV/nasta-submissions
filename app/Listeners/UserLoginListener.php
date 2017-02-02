<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Events\Login;

use Carbon\Carbon;

use Auth;

class UserLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  auth.login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = Auth::user();
        $user->last_login_at = Carbon::now();
        $user->save();
    }
}
