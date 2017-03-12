<?php 
namespace App\Helpers;

class StringHelper {

  public static function formatBytes($size, $precision = 2)
  {
    if ($size == 0)
      return "0B";

    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
  }

  public static function formatDuration($durationInSeconds) {
    $duration = '';
    $minutes = floor($durationInSeconds / 60);
    $seconds = floor($durationInSeconds - $minutes * 60);

    if($minutes > 0) {
      $duration .= ' ' . $minutes . 'm';
    }
    if($seconds > 0) {
      $duration .= ' ' . $seconds . 's';
    }
    return $duration;
  }
}