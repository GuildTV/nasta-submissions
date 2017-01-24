<?php
namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\UploadException;

use App\Http\Requests\Station\SubmitRequest;

use App\Mail\Station\EntrySubmitted;

use App\Database\Category\Category;

use App;
use Auth;
use Redirect;
use Mail;
use Session;

class EntryController extends Controller
{ 
  public function submit(SubmitRequest $request, Category $category)
  {
    if (!$category->canEditSubmissions())
      return App::abort(400);

    $entry = $category->getEntryForStation($request->user()->id); // Gets an empty entry

    $sendSubmittedEmail = false;
    if ($request->has('submit') && $request->input('submit') && !$entry->submitted)
      $sendSubmittedEmail = true;

    $entry->name = $request->input('name');
    $entry->description = $request->input('description');
    $entry->rules = $request->has('rules') && $request->input('rules');
    $entry->submitted = $request->has('submit') && $request->input('submit');
    $entry->save();

    Mail::to($request->user())->queue(new EntrySubmitted($entry));

    Session::flash('entry.save', 'Your changes have been saved');

    return $entry;
  }

  public function edit(Category $category){
    if (!$category->canEditSubmissions())
      return App::abort(400);

    $entry = $category->getEntryForStation(Auth::user()->id); // Gets an empty entry
    $entry->submitted = false;
    $entry->save();

    Session::flash('entry.edit', 'You entry has been un-submitted');

    return $entry; 
  }

  public function init_upload(){
    $user = Auth::user();

    $folder = $user->stationFolder;
    if ($folder == null)
      throw new UploadException("Missing target for user");

    return Redirect::to($folder->request_url);
  }

}
