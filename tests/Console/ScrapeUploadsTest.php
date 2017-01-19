<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;

use App\Console\Commands\ScrapeUploads;

use Carbon\Carbon;
use Config;

class ScrapeUploadsTest extends TestCase
{
  use DatabaseTransactions;

  private static $dummyHelperClass = "App\Helpers\Files\TestFileServiceHelper";
  private static $debugHelper = false;

  private static $testCategoryId = "animation";
  private static $testCategoryCompact = "Male";

  public function testScrapeFailLoadFolder(){
    $scraper = new ScrapeUploads();
    $folder = StationFolder::find(1);
    $helper = new self::$dummyHelperClass([], self::$debugHelper); // Initialise to fail with no files

    $expectedOps = [
      [ "list", $folder->folder_name ]
    ];

    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals("FAILED_LIST", $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
  }

  public function testScrapeImportSingleFile(){
    $folder = StationFolder::find(1);
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/blah.mp4", 
        "modified" => Carbon::now(), 
        "size" => 12443, 
        "rev" => "sgsdsgdg", 
        "hash" => "hfisisfsd",
      ]
    ];
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetDir . "blah_".$files[0]['hash'].".mp4" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    $this->assertEmailSent();

    $file = UploadedFile::where("path", $expectedOps[1][2])->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
  }

  public function testScrapeImportMatchCategory(){
    $folder = StationFolder::find(1);
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/".self::$testCategoryCompact."_station_entryname.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetDir . self::$testCategoryCompact."_station_entryname_".$files[0]['hash'].".mp4" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    $this->assertEmailSent();

    $file = UploadedFile::where("path", $expectedOps[1][2])->first();
    $this->assertNotNull($file);
    $this->assertEquals(self::$testCategoryId, $file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
  }

  public function testScrapeImportMatchNamePrefix(){
    $folder = StationFolder::find(1);
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/Soem person - blah_entryname.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetDir . "blah_entryname_".$files[0]['hash'].".mp4" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    $this->assertEmailSent();

    $file = UploadedFile::where("path", $expectedOps[1][2])->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
  }

  // TODO - test hitting EntryFileMadeLate
  // TODO - test hitting EntryFileAlreadySubmitted
  // TODO - test hitting EntryFileCloseDeadline

}