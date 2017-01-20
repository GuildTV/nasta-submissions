<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\User;
use App\Database\Category\Category;
use App\Database\Entry\Entry;

use App;

class SubmissionsController extends Controller
{ 

  public function dashboard()
  {
    $users = User::where('type', 'station')->orderBy('name', 'asc')->get();
    $categories = Category::orderBy('name', 'asc')->get();

    return view('admin.submissions.dashboard', compact('users', 'categories'));
  }

  public function category(Category $category)
  {
    $users = User::where('type', 'station')->orderBy('name', 'asc')->get();
    $entries = Entry::where('category_id', $category->id)->get();

    return view('admin.submissions.category', compact('category', 'users', 'entries'));
  }

  public function station(User $station)
  {
    $categories = Category::orderBy('name', 'asc')->get();
    $entries = Entry::where('station_id', $station->id)->get();

    return view('admin.submissions.station', compact('station', 'categories', 'entries'));
  }

  public function view(User $station, Category $category)
  {
    $entry = Entry::where('station_id', $station->id)->where('category_id', $category->id)->first();
    if ($entry == null)
      App::abort(404);

    return view('admin.submissions.view', compact('station', 'category', 'entry'));
  }

}
