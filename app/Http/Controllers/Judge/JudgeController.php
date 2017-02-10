<?php
namespace App\Http\Controllers\Judge;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Judge\ScoreRequest;

use App\Mail\Judge\CategoryJudged;

use App\Database\Category\Category;
use App\Database\Category\CategoryResult;
use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;
use App\Database\Upload\UploadedFile;

use Auth;
use App;
use Redirect;
use Mail;
use Config;

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
      ->with('entries')->with('entries.station')->with('entries.result')->with('entries.rule_break')
      ->get();
    $adminVersion = false;

    return view('judge.dashboard', compact('categories', 'adminVersion'));
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

    $readonly = $entry->category->isResultsReadOnly();

    return view('judge.view', compact('entry', 'constraint_map', 'readonly'));
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

    if ($entry->category->isResultsReadOnly())
      return App::abort(403);

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

  public function finalize(Request $request, Category $category)
  {
    if ($category->judge_id != Auth::user()->id)
      return App::abort(404);

    if ($category->isResultsReadOnly())
      return App::abort(403);

    // ensure the specified winners are valid
    $winner = Entry::find($request->winner_id);
    $commended = Entry::find($request->commended_id);
    $count = ($winner == null ? 0 : 1) + ($commended == null ? 0 : 1);
    $entryCount = $category->entries()->count();

    // if we dont have the correct number of 'winners' then fail
    if (min($entryCount, 2) != $count)
      return App::abort(422);

    if ($winner != null && $winner->category_id != $category->id)
      return App::abort(422);
    if ($commended != null && $commended->category_id != $category->id)
      return App::abort(422);

    $data = [
      'category_id' => $category->id,
    ];

    if ($winner != null) {
      $data['winner_id'] = $winner->id;
      $data['winner_comment'] = $request->has('winner_comment') ? $request->winner_comment : "";
    }
    if ($commended != null) {
      $data['commended_id'] = $commended->id;
      $data['commended_comment'] = $request->has('commended_comment') ? $request->commended_comment : "";
    }

    $result = CategoryResult::create($data);

    // email host/admins to notify of the finish!
    foreach (User::where('type', 'admin')->get() as $user){
      Mail::to($user)->queue(new CategoryJudged($category));
    }

    return $result;
  }

}