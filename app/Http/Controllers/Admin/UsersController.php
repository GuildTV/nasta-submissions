<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\User;
use App\Database\Upload\DropboxAccount;

use App\Http\Requests\Admin\UserSaveRequest;
use App\Http\Requests\Admin\UserDropboxSaveRequest;

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
    $dropboxAccounts = DropboxAccount::all();
    return view('admin.users.view', compact('user', 'dropboxAccounts'));
  }

  public function save(UserSaveRequest $request, User $user)
  {
    $user->name = $request->name;
    $user->username = $request->username;
    $user->compact_name = $request->compact_name;
    $user->email = $request->email;

    if ($request->has('password'))
      $user->password = bcrypt($request->password);
    
    $user->save();

    return $user;
  }

}