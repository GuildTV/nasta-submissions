<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{ 
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function dashboard()
  {
    $version = @file_get_contents(app_path('version.tmp'));
    if ($version == null)
      $version = "dev";

    return view('admin.dashboard', compact('version'));
  }

}
