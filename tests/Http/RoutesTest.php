<?php
namespace Tests\Http;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use AutoTestBase;

use App\Database\User;

use Illuminate\Routing\Route as IllRoute;

use Route;

/**
 * Test all of the webpage routes in the router to check for any exceptions when rendering.
 * Not guaranteed to catch every case, and might need to be disabled for some routes.
 * Works based on various combinations of data already in the database
 */
class RoutesTest extends AutoTestBase
{
  use DatabaseTransactions;

  // Route names to skip
  const SKIP_NAMES = [
    'auth.reset',
    'debugbar.',
    'station.entry.upload',
    'admin.rule-break.file-state',
    'admin.rule-break.entry-state',
    'admin.rule-break.file-check',
    'admin.rule-break.entry-check',
  ];

  // route, params, user
  const EXPECTED_EXCEPTIONS = [
    [ "station.entry.upload", [ "already-closed" ], null],
    [ "station.entry", [ 'no-constraints' ], null],
  ];

  const ALLOWED_RESPONSE_STATUS = [
    200, 302,
    404
  ];

  protected $requestCount;

  public function testRoutes()
  {
    $routeCollection = Route::getRoutes();

    $validRoutes = [];

    foreach ($routeCollection as $route) {
      $prefix = $route->getPrefix();
      if($prefix != null && strpos($prefix, "api") === 0)
        continue;

      if($route->getMethods()[0] != "GET")
        continue;

      if ($this->shouldSkipRoute($route->getName()))
        continue;

      $validRoutes[] = $route;
    }

    $routes = collect($validRoutes);

    // load all users to test with
    $this->stations = User::where('type', 'station')->get();
    $this->assertTrue(count($this->stations) > 0);
    $this->judges = User::where('type', 'judge')->get();
    $this->assertTrue(count($this->judges) > 0);

    print "\n\n---\nTesting " . $routes->count() . " routes\n";

    $this->requestCount = 0;

    foreach ($routes as $r) {
      $this->runRoute($r);
    }

    print "\nFinished " . $routes->count() . " routes with " . $this->requestCount . " requests\n---\n\n";
  }

  private function shouldSkipRoute($routeName){
    foreach (self::SKIP_NAMES as $name){
      if (strrpos($name, ".") == strlen($name)-1 && strpos($routeName, $name) === 0)
        return true;

      if ($routeName == $name)
        return true;
    }

    return false;
  }

  private function runRoute(IllRoute $route)
  {
    $paramClasses = $this->getRouteParams($route);
    $combinations = $this->compileParamCombinations($paramClasses, true);

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

    $prefix = $route->getPrefix();

    // test admin pages
    if($prefix != null && (strpos($prefix, "admin") === 0 || strpos($prefix, "admin") === 1)) {
      $this->sendRequest($route, $action, $params, $this->admin);
    }
    // test station pages
    else if($prefix != null && (strpos($prefix, "station") === 0 || strpos($prefix, "station") === 1)) {
      foreach ($this->stations as $station) {
        $this->sendRequest($route, $action, $params, $station);
      }
    }
    // test judge pages
    else if($prefix != null && (strpos($prefix, "judge") === 0 || strpos($prefix, "judge") === 1)) {
      foreach ($this->judges as $judge) {
        $this->sendRequest($route, $action, $params, $judge);
      }
    } 
    // public page
    else {
      $this->sendRequest($route, $action, $params, null);
    }
  }

  private function sendRequest($route, $action, $params, $user){
    $req = $this;
    if ($user != null)
      $req = $req->actingAs($user);

    $paramIds = [];
    foreach ($params as $p){
      $paramIds[] = $p['id'];
    }

    $this->requestCount++;
    $response = $req->action('GET', $action, $params);
    if ($response->status() == 500) {
      foreach(self::EXPECTED_EXCEPTIONS as $ex){
        if ($ex[0] != $route->getName())
          continue;

        if ($paramIds != $ex[1])
          continue;

        if ($ex[2] != null && $user != null && $user->id != $ex[2])
          continue;

        // valid match
        return;
      }
    }

    $this->assertContains($response->status(), self::ALLOWED_RESPONSE_STATUS, sprintf("Route: %s, Params: [ %s ], User: %s, Code: %d, Response: %s", $route->getName(), implode(", ", $paramIds), $user ? $user->id : null, $response->status(), $response->getContent()));
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

}