<?php

namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DataIntegrityException;

use App\Http\Requests\Station\SubmitRequest;

use App\Database\Category\Category;

use DB;
use Auth;
use App;

class EntryController extends Controller
{ 
  public function submit(SubmitRequest $request, Category $category)
  {
    DB::beginTransaction();

    $category->entries()->where('station_id', Auth::user()->id)->delete();

    $entry = $category->getEntryForStation(Auth::user()->id); // Gets an empty entry

    $entry->name = $request->input('name');
    $entry->description = $request->input('description');
    $entry->rules = $request->has('rules') && $request->input('rules');
    $entry->submitted = $request->has('submit') && $request->input('submit');
    $entry->save();

    DB::commit();

    return $entry;
  }

}
