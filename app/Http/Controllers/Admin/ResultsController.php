<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\Category\Category;

class ResultsController extends Controller
{ 

  public function dashboard()
  {
    $categories = Category::orderBy('name', 'asc')
      ->with('result')->get();

    return view('admin.results', compact('categories'));
  }

  public function view(Category $category)
  {
    $category->load('entries.station', 'entries.result');

    $categories = [ $category ];
    $adminVersion = true;

    return view('judge.dashboard', compact('categories', 'adminVersion'));
  }


}