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

  public function saveDropbox(UserDropboxSaveRequest $request, User $user)
  {
    $folder = $user->stationFolderOrNew();
    $folder->account_id = $request->account;
    $folder->request_url = $request->url;
    $folder->folder_name = $request->folder;

    $folder->save();

    return $folder;
  }

}