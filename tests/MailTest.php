<?php
namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use AutoTestBase;

use Illuminate\Filesystem\ClassFinder;

use App\Exceptions\InvalidArgumentException;

use \ReflectionClass;

use Mail;

/**
 * Test all of the webpage routes in the router to check for any exceptions when rendering.
 * Not guaranteed to catch every case, and might need to be disabled for some routes.
 * Works based on various combinations of data already in the database
 */
class MailTest extends AutoTestBase
{
  const TARGET_EMAIL = "fakeemail@nasta.tv";

  public function testMails()
  {
    $finder = new ClassFinder();
    $files = $finder->findClasses(app_path("Mail"));

    print "\n\n---\nTesting " . count($files) . " mail templates\n";

    $this->emailCount = 0;

    foreach ($files as $f) {
      $this->runEmail($f);
    }

    print "\nFinished " . count($files) . " mail templates with " . $this->emailCount . " emails\n---\n\n";
  }

  private function runEmail($classname)
  {
    $paramClasses = $this->getMailParams($classname);
    $combinations = $this->compileParamCombinations($paramClasses);

    $combCount = count($combinations);
    if($combCount == 0)
      $combCount ++;

    // hard limit on number of combinations. can be increased, as long as the test doesnt take too long to run
    $this->assertLessThan(100, $combCount);

    print "Running mail template: " . $classname . " with " . count($paramClasses) . " params and " . $combCount . " combinations\n";

    if($combinations->count() == 0){
      $this->runForEmail($classname, []);
    }

    foreach($combinations as $comb){
      $this->runForEmail($classname, $comb);
    }
  }

  private function runForEmail($classname, $comb)
  {
    $class = new ReflectionClass($classname);

    try {
      $mail = $class->newInstanceArgs($comb);
    } catch (InvalidArgumentException $e) {
      return;
    }

    Mail::to(self::TARGET_EMAIL)->send($mail);
    $this->emailCount++;
  }

  private function getMailParams($classname)
  {
    $paramClasses = [];

    $class = new ReflectionClass($classname);
    $constructor = $class->getConstructor();

    foreach($constructor->getParameters() as $param){
      $class = $param->getClass();
      $name = $param->getName();

      if($class == null || $name == null || $name == "")
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