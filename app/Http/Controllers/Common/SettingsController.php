<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DataIntegrityException;

use App\Database\User;

use App\Http\Requests\Common\SettingsRequest;

use App;
use Auth;
use Exception;

class SettingsController extends Controller
{
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function settings()
  {
    $user = Auth::user();
    return view('common.settings', compact('user'));
  }

  public function save(SettingsRequest $request)
  {
    $user = Auth::user();

    try {
      $user->email = $request->email;

      if ($request->has('password'))
        $user->password = bcrypt($request->password);
      
      $user->save();
    } catch (Exception $e){
      return App::abort(422, "Email address already in use");
    }

    return $user;
  }

}