<?php
namespace App\Http\Controllers\Judge;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Judge\ScoreRequest;

use App\Database\Category\Category;
use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;
use App\Database\Upload\UploadedFile;

use Auth;
use App;
use Redirect;

class JudgeController extends Controller
{ 
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function dashboard()
  {
    $categories = Category::where('judge_id', Auth::user()->id)
      ->with('entries')->with('entries.station')->with('entries.result')
      ->get();

    return view('judge.dashboard', compact('categories'));
  }

  public function view(Entry $entry)
  {
    if ($entry->category->judge_id != Auth::user()->id)
      return App::abort(404);

    if (!$entry->canBeJudged())
      return App::abort(403);

    $constraint_map = [];
    if ($entry->rule_break != null)
      $constraint_map = json_decode($entry->rule_break->constraint_map, true);

    return view('judge.view', compact('entry', 'constraint_map'));
  }

  public function download(UploadedFile $file)
  {
    // Check file belongs to user
    if ($file->category_id == null || $file->category->judge_id != Auth::user()->id)
      return App::abort(404);
    
    $url = $file->getUrl();
    if ($url == null)
      return App::abort(404);
    
    return Redirect::to($url);
  }

  public function score(ScoreRequest $request, Entry $entry)
  {
    if ($entry->category->judge_id != Auth::user()->id)
      return App::abort(404);

    if (!$entry->canBeJudged())
      return App::abort(403);

    $result = $entry->result;
    if ($result == null)
      $result = new EntryResult([ "entry_id" => $entry->id ]);

    $result->score = $request->score;

    if ($request->has('feedback'))
      $result->feedback = $request->feedback;
    else
      $result->feedback = "";

    $result->save();

    return $result;
  }

}