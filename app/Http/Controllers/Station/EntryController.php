<?php
namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\UploadException;

use App\Http\Requests\Station\SubmitRequest;

use App\Database\Category\Category;

use Auth;
use Redirect;

class EntryController extends Controller
{ 
  public function submit(SubmitRequest $request, Category $category)
  {
    $entry = $category->getEntryForStation(Auth::user()->id); // Gets an empty entry

    $entry->name = $request->input('name');
    $entry->description = $request->input('description');
    $entry->rules = $request->has('rules') && $request->input('rules');
    $entry->submitted = $request->has('submit') && $request->input('submit');
    $entry->save();

    return $entry;
  }

  public function edit(Category $category){
    $entry = $category->getEntryForStation(Auth::user()->id); // Gets an empty entry
    $entry->submitted = false;
    $entry->save();

    return $entry; 
  }

  public function init_upload(){
    $user = Auth::user();

    $folder = $user->station_folder;
    if ($folder == null)
      throw new UploadException("Missing target for user");

    return Redirect::to($folder->request_url);
  }

}
