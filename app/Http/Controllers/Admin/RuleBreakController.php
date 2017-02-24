<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AjaxRequest;

use App\Database\Entry\Entry;
use App\Database\Category\FileConstraint;
use App\Database\Upload\UploadedFile;

use App\Jobs\OfflineRuleCheckEntry;
use App\Jobs\OfflineRuleCheckFile;
use App\Jobs\Encode\QueueEncode;


use App;
use Redirect;
use Config;

class RuleBreakController extends Controller
{ 
  public function index(Entry $entry)
  {
    $files = $entry->allUploadedFiles->load('rule_break');

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

  public function errors()
  {
    $files = UploadedFile::with('rule_break')->get();

    $errors = [];
    $warnings = [];

    foreach ($files as $file){
      if ($file->rule_break == null)
        continue;

      $errs = json_decode($file->rule_break->errors, true);
      foreach ($errs as $err){
        if (!isset($errors[$err]))
          $errors[$err] = [ "total"=>0, "pending"=>0 ];

        $errors[$err]["total"]++;
        if ($file->rule_break->result != "accepted")
          $errors[$err]["pending"]++;
      }

      $warns = json_decode($file->rule_break->warnings, true);
      foreach ($warns as $warn){
        if (!isset($warnings[$warn]))
          $warnings[$warn] = [ "total"=>0, "pending"=>0 ];

        $warnings[$warn]["total"]++;
        if ($file->rule_break->result != "accepted")
          $warnings[$warn]["pending"]++;
      }
    }

    return view('admin.rule-break.errors', compact('errors', 'warnings'));
  }

  public function entry_save(AjaxRequest $r, Entry $entry){
    if ($entry->rule_break == null)
      return App::abort(404);

    $entry->rule_break->result = $r->result;
    $entry->rule_break->notes = $r->notes;
    $entry->rule_break->save();

    return $entry;
  }

  public function entry_recheck(Entry $entry){
    dispatch(new OfflineRuleCheckEntry($entry, true));

    return Redirect::route('admin.rule-break', $entry);
  }

  public function file_save(AjaxRequest $r, UploadedFile $file){
    if ($file->rule_break == null)
      return App::abort(404);

    $file->rule_break->result = $r->result;
    $file->rule_break->notes = $r->notes;
    $file->rule_break->save();

    return $file;
  }

  public function file_recheck(Entry $entry, UploadedFile $file){
    dispatch((new OfflineRuleCheckFile($file, true))->onQueue("process"));

    return Redirect::route('admin.rule-break', $entry);
  }

  public function transcode(UploadedFile $file, $profile){
    $options = Config::get('nasta.encode_profiles');
    if (!isset($options[$profile]))
      return App::abort(404);

    $profile_id = $options[$profile];
    dispatch((new QueueEncode($file, $profile_id))->onQueue('process'));

    return "OK";
  }

}
