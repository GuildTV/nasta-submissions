<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;

use App\Helpers\DropboxHelper;

use App\Console\Commands\ScrapeUploads;

use Carbon\Carbon;

class ScrapeUploadsTest extends TestCase
{
  use DatabaseTransactions;

  private static $testCategory = "animation";
  private static $testClosedCategory = "something";

  private static $deleteUrl = '/station/files/%d/delete';
  private static $linkUrl = '/station/files/%d/link/%s';

  private static $testAccountId = "test";
  private static $testSourceFile = "/test/test_file.png";



  public function testssdgsd(){
    $scraper = new ScrapeUploads();

    $folder = StationFolder::find(1);

    $res = $scraper->scrapeFolder($folder);
    $this->assertNull($res);
  }




}