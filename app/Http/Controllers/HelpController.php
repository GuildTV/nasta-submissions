<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class HelpController extends Controller
{

  public function rules()
  {
    return view('help.rules');
  }

  public function video_format()
  {
    return view('help.video-format');
  }

  public function contact()
  {
    return view('help.contact');
  }
  
}
