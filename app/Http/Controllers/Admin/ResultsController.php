<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\User;
use App\Database\Category\Category;
use App\Database\Entry\Entry;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;

use App\Jobs\DropboxScrapeMetadata;

use App;
use Redirect;

class ResultsController extends Controller
{ 

  public function dashboard()
  {
    $categories = Category::orderBy('name', 'asc')->get();

    return view('admin.results', compact('categories'));
  }

  public function view(Category $category)
  {
    $categories = [ $category ];
    $adminVersion = true;

    return view('judge.dashboard', compact('categories', 'adminVersion'));
  }


}