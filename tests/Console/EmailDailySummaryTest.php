<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;
use App\Database\Entry\Entry;
use App\Database\User;

use App\Console\Commands\EmailDailySummary;

use Carbon\Carbon;
use Config;

class EmailDailySummaryTest extends TestCase
{
  use DatabaseTransactions;

  public function testNoDeadlines(){
    $scraper = new EmailDailySummary();
    $res = $scraper->handle();
    $this->assertEquals("NO_DEADLINES", $res);
  }

  private function createDeadline(){
    return Category::create([
      'id' => str_random(10),
      'name' => str_random(10),
      'compact_name' => str_random(10),
      'description' => str_random(10),
      'closing_at' => Carbon::now(),
    ]);
  }

  public function testNoUsers(){
    $this->createDeadline();

    User::where('type', 'station')->delete();

    $scraper = new EmailDailySummary();
    $res = $scraper->handle();
    $this->assertEquals("NO_USERS", $res);
  }

  public function testSend(){
    $this->createDeadline();
    $cat = $this->createDeadline();

    $entry = new Entry([
      'station_id' => $this->station->id,
      'name' => str_random(10), 
      'description' => str_random(50),
    ]);
    $entry->category_id = $cat->id;
    $entry->rules = true;
    $entry->submitted = true;
    $entry->save();

    $scraper = new EmailDailySummary();
    $res = $scraper->handle();
    $this->assertEquals(2, $res);
    $this->assertEmailSent();
  }

}