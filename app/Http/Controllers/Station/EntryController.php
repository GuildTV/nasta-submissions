<?php
namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\UploadException;

use App\Http\Requests\Station\SubmitRequest;

use App\Mail\Station\EntrySubmitted;

use App\Database\Category\Category;
use App\Database\Entry\EntryRuleBreak;

use Carbon\Carbon;

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

    if ($sendSubmittedEmail)
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

    // delete any entry rule breaks
    EntryRuleBreak::where('entry_id', $entry->id)->delete();

    Session::flash('entry.edit', 'You entry has been un-submitted');

    return $entry; 
  }

  public function init_upload(Category $category){
    $user = Auth::user();

    $folder = $user->stationFolders()->where('category_id', $category->id)->first();
    if ($folder == null)
      throw new UploadException("Missing target for user");

    $folder->last_accessed_at = Carbon::now();
    $folder->save();

    // TODO - fallback to shared folder?

    return Redirect::to($folder->request_url);
  }

}
