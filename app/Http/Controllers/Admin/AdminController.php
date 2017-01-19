<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App;
use Auth;

class AdminController extends Controller
{ 
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function dashboard()
  {
    return view('admin.dashboard');
  }

}
