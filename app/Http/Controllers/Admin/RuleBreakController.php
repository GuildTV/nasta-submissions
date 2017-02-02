<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\Entry\Entry;
use App\Database\Category\FileConstraint;
use App\Database\Upload\UploadedFile;

use App\Jobs\OfflineRuleCheckEntry;
use App\Jobs\OfflineRuleCheckFile;

use App;
use Redirect;

class RuleBreakController extends Controller
{ 
  public function index(Entry $entry)
  {
    $files = $entry->uploadedFiles->load('rule_break');

    $constraint_map = [];
    if ($entry->rule_break != null){
      $constraint_map = json_decode($entry->rule_break->constraint_map, true);
      $constraint_map = array_map(function($c){
        $con = FileConstraint::find($c);
        if ($con == null)
          return "???";

        return $con->name;
      }, $constraint_map);
    }

    return view('admin.submissions.rule-break', compact('files', 'entry', 'constraint_map'));
  }

  public function entry_accept_reject(Entry $entry, $state){
    switch ($state){
      case "accepted":
      case "rejected":
        break;
      default:
        return App::abort(404);
    }

    if ($entry->rule_break == null)
      return App::abort(404);

    $entry->rule_break->result = $state;
    $entry->rule_break->save();

    return Redirect::route('admin.rule-break', $entry);
  }

  public function entry_recheck(Entry $entry){
    dispatch(new OfflineRuleCheckEntry($entry, true));

    return Redirect::route('admin.rule-break', $entry);
  }

  public function file_accept_reject(Entry $entry, $state, UploadedFile $file){
    switch ($state){
      case "accepted":
      case "rejected":
        break;
      default:
        return App::abort(404);
    }

    if ($file->rule_break == null)
      return App::abort(404);

    $file->rule_break->result = $state;
    $file->rule_break->save();

    return Redirect::route('admin.rule-break', $entry);
  }

  public function file_recheck(Entry $entry, UploadedFile $file){
    dispatch((new OfflineRuleCheckFile($file, true))->onQueue("downloads"));

    return Redirect::route('admin.rule-break', $entry);
  }

}
