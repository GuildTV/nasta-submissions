<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class AutoTestBase extends TestCase
{

  protected function compileParamCombinations($paramClasses)
  {
    $paramValues = [];

    foreach($paramClasses as $name=>$class){
      $paramValues[$name] = $class->getMethod("query")->invoke(null)->get();
    }

    $keys = array_keys($paramValues);

    return $this->buildCombinations($paramValues, $keys, []);

  }

  private function buildCombinations($paramValues, $keys, $prefix)
  {
    if(count($keys) == 0)
      return collect([]);;

    $results = collect([]);

    $key = array_shift($keys);
    $params = $paramValues[$key];

    foreach($params as $p){
      $arr = array_merge($prefix, []);
      $arr[$key] = $p;

      $r = $this->buildCombinations($paramValues, $keys, $arr);
      if($r->count() == 0) {
        $results->push($arr);
      } else {
        foreach($r as $s)
          $results->push($s);
      }
    }

    return $results;
  }

}