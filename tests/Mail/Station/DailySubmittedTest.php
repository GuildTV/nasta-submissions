<?php
namespace Tests\Mail\Station;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Category\Category;

use App\Mail\Station\DailySubmitted;

use Mail;

use Carbon\Carbon;

class DailySubmittedTest extends TestCase
{
  const TARGET_EMAIL = "fakeemail@nasta.tv";

  // public function testSingle()
  // {
  //   $mail = new DailyDeadlines($this->station, null);
  //   Mail::to(self::TARGET_EMAIL)->send($mail);
  // }

  public function testDays()
  {
    $dates = $this->getDates();
    $this->assertEquals(5, count($dates));

    foreach ($dates as $date=>$count) {
      $mail = new DailySubmitted($this->station, Carbon::parse($date));
      $this->assertEquals($count, count($mail->categories));

      Mail::to(self::TARGET_EMAIL)->send($mail);
    }
    
    $this->assertEmailCount(count($dates));
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