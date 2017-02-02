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

  public function files()
  {
    $files = UploadedFile::with('category')->with('station')->get();

    return view('admin.submissions.files', compact('files'));
  }

  public function file(UploadedFile $file)
  {
    $categories = Category::orderBy('name', 'asc')->get();
    
    $entry = null;
    $category = $file->category;
    if ($category != null){
      $entry = $category->getEntryForStation($file->station_id);
      if ($entry->id == null)
        $entry = null;
    }

    return view('admin.submissions.file', compact('file', 'categories', 'entry'));
  }

  public function linkfile(UploadedFile $file, Category $category)
  {
    // update file
    $file->category_id = $category->id;
    $file->save();

    UploadedFileLog::create([
      'station_id' => $file->station_id,
      'uploaded_file_id' => $file->id,
      'category_id' => $file->category_id,
      'level' => 'info',
      'message' => 'Admin linked file \'' . $file->name . '\' to category \'' . $category->name . '\'',
    ]);

    return $file;
  }

  public function download(UploadedFile $file)
  {
    $url = $file->getUrl();
    if ($url == null)
      return App::abort(404);
    
    return Redirect::to($url);
  }

  public function metadata(UploadedFile $file)
  {
    dispatch(new DropboxScrapeMetadata($file));
    return Redirect::route("admin.submissions.file", $file);
  }

}
