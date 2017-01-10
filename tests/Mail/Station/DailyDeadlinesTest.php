<?php
namespace Tests\Mail\Station;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use Illuminate\Filesystem\ClassFinder;

use App\Database\Category\Category;

use App\Mail\Station\DailyDeadlines;

use Mail;

use Carbon\Carbon;

/**
 * Test all of the webpage routes in the router to check for any exceptions when rendering.
 * Not guaranteed to catch every case, and might need to be disabled for some routes.
 * Works based on various combinations of data already in the database
 */
class DailyDeadlinesTest extends TestCase
{
  const TARGET_EMAIL = "fakeemail@nasta.tv";


  public function testSingle()
  {
    $mail = new DailyDeadlines($this->station, null);
    Mail::to(self::TARGET_EMAIL)->send($mail);
  }

  public function testDays()
  {
    $dates = $this->getDates();
    $this->assertEquals(2, count($dates));

    foreach ($dates as $date=>$count) {
      $mail = new DailyDeadlines($this->station, Carbon::parse($date));
      $this->assertEquals(1, count($mail->groupedCategories));
      $this->assertEquals($count, count($mail->groupedCategories[$date]));

      Mail::to(self::TARGET_EMAIL)->send($mail);
    }
  }

  private function getDates(){
        $res = Category::all();

        $dates = [];

        foreach ($res as $cat) {
            $date = $cat->closing_at->startOfDay()->toIso8601String();

            if (!isset($dates[$date]))
              $dates[$date] = 0;

            $dates[$date]++;
        }

        return $dates;
    }

}