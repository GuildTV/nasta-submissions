<?php
namespace Tests\Http;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use Illuminate\Routing\Route as IllRoute;

use Route;

/**
 * Test all of the webpage routes in the router to check for any exceptions when rendering.
 * Not guaranteed to catch every case, and might need to be disabled for some routes.
 * Works based on various combinations of data already in the database
 */
class RoutesTest extends TestCase
{
  // use DatabaseTransactions;

  // Route names to skip
  const SKIP_NAMES = [
    'auth.reset',
  ];

  protected $requestCount;

  public function testThings()
  {
    $routeCollection = Route::getRoutes();

    $validRoutes = [];

    foreach ($routeCollection as $route) {
      $prefix = $route->getPrefix();
      if($prefix != null && strpos($prefix, "api") === 0)
        continue;

      if($route->getMethods()[0] != "GET")
        continue;

      if(in_array($route->getName(), self::SKIP_NAMES))
        continue;

      $validRoutes[] = $route;
    }

    $routes = collect($validRoutes);

    print "\n\n---\nTesting " . $routes->count() . " routes\n";

    $this->requestCount = 0;

    foreach ($routes as $r) {
      $this->runRoute($r);
    }

    print "\nFinished " . $routes->count() . " routes with " . $this->requestCount . " requests\n---\n\n";
  }

  private function runRoute(IllRoute $route)
  {
    $paramClasses = $this->getRouteParams($route);
    $combinations = $this->compileParamCombinations($paramClasses);

    $combCount = $combinations->count();
    if($combCount == 0)
      $combCount ++;

    // hard limit on number of combinations. can be increased, as long as the test doesnt take too long to run
    $this->assertLessThan(100, $combCount);

    $name = $route->getName();
    print "Running route: " . ($name ? $name . " - " : "") . "/" . $route->getPath() . " with " . count($paramClasses) . " params and " . $combCount . " combinations\n";

    if($combinations->count() == 0){
      $this->runForRoute($route, []);
    }

    foreach($combinations as $comb){
      $this->runForRoute($route, $comb);
    }
  }

  private function runForRoute(IllRoute $route, $params)
  {
    $action = $route->getActionName();
    if(strpos($action, "App\\Http\\Controllers\\") === 0)
      $action = substr($action, 21);

    $this->requestCount++;

    $prefix = $route->getPrefix();

    // test admin pages
    if($prefix != null && (strpos($prefix, "admin") === 0 || strpos($prefix, "admin") === 1)) {
      $response = $this->actingAs($this->admin)->action('GET', $action, $params);
      $this->assertContains($response->status(), [200,302], $response->getContent());
    }
    // test station pages
    else if($prefix != null && (strpos($prefix, "station") === 0 || strpos($prefix, "station") === 1)) {
      $response = $this->actingAs($this->station)->action('GET', $action, $params);
      $this->assertContains($response->status(), [200,302], $response->getContent());
    }
    // test judge pages
    else if($prefix != null && (strpos($prefix, "judge") === 0 || strpos($prefix, "judge") === 1)) {
      $response = $this->actingAs($this->judge)->action('GET', $action, $params);
      $this->assertContains($response->status(), [200,302], $response->getContent());
    } 
    // public page
    else {
      $response = $this->action('GET', $action, $params);
      $this->assertContains($response->status(), [200,302], $response->getContent());
    }
  }

  private function getRouteParams(IllRoute $route)
  {
    $paramClasses = [];

    foreach($route->signatureParameters() as $param){
      $class = $param->getClass();
      $name = $param->getName();

      if($class == null || $name == null || $name == "")
        continue;

      if(is_a($class->name, "\Illuminate\Http\Request", true))
        continue;

      if(!is_a($class->name, "\Illuminate\Database\Eloquent\Model", true))
        continue;

      if(!$class->hasMethod("query"))
        continue;

      $paramClasses[$name] = $class;
    }

    return $paramClasses;
  }

  private function compileParamCombinations($paramClasses)
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