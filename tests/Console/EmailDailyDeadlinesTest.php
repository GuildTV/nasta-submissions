<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;
use App\Database\User;

use App\Console\Commands\EmailDailyDeadlines;

use Carbon\Carbon;
use Config;

class EmailDailyDeadlinesTest extends TestCase
{
  use DatabaseTransactions;

  public function testNoDeadlines(){
    $scraper = new EmailDailyDeadlines();
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

    $scraper = new EmailDailyDeadlines();
    $res = $scraper->handle();
    $this->assertEquals("NO_USERS", $res);
  }

  public function testSend(){
    $this->createDeadline();

    $scraper = new EmailDailyDeadlines();
    $res = $scraper->handle();
    $this->assertEquals(User::where('type', 'station')->count(), $res);
    $this->assertEmailSent();
  }

}