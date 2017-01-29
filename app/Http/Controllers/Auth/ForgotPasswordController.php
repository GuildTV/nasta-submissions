<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Database\User;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['username' => 'required']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('username')
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponseOverride($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponseOverride(Request $request, $response)
    {
        $user = User::where('username', $request->username)->first();
        $email = "???";
        if ($user != null)
            $email = $this->hideEmail($user->email);

        return back()->with('status', trans($response, [ 'email' => $email ]));
    }

    private function hideEmail($input,$show=3) {
       $arr = explode('@', $input);
       $arr[0] = substr($arr[0],0,$show).str_repeat('*',max(strlen($arr[0])-$show, 0));

       $arr2 = explode('.', $arr[1]);
       $arr2[0] = substr($arr2[0],0,$show).str_repeat('*',max(strlen($arr2[0])-$show, 0));

       $arr[1] = implode('.', $arr2);
       return implode("@", $arr);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors(
            ['username' => trans($response)]
        );
    }
}
