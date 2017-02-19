<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

use DB;

class StatusController extends Controller
{

  public function status()
  {
    $maxLengths = [
      'default' => 200,
      'downloads' => 20
    ];

    $jobs = DB::table('jobs')->select(DB::raw('queue, COUNT(id) AS count'))->groupBy('queue')->get();

    $res = [
      'queues' => [],
    ];

    foreach ($jobs as $job){
      $id = $job->queue;

      $max = isset($maxLengths[$id]) ? $maxLengths[$id] : 0;

      $res['queues'][$id] = strtoupper(($job->count > $max ? "OVER_" : "OK_") . $id . ":" . $job->count);
    }



    return $res;
  }
}
