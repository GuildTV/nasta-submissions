<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\User;

use App\Http\Requests\Admin\UserSaveRequest;

use App;
use Exception;

class UsersController extends Controller
{ 
  public function dashboard()
  {
    $users = User::all();

    return view('admin.users.dashboard', compact('users'));
  }

  public function view(User $user)
  {
    return view('admin.users.view', compact('user'));
  }

  public function save(UserSaveRequest $request, User $user)
  {
    try {
      $user->name = $request->name;
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