<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;

use App\Helpers\Files\DropboxFileServiceHelper;

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
      [ "name" => $folder->folder_name . "/blah.mp4", "modified" => Carbon::now(), "size" => 12443, "rev" => "sgsdsgdg" ]
    ];
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetDir . "blah_".$files[0]['rev'].".mp4" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());

    $file = UploadedFile::where("path", $expectedOps[1][2])->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
  }

  public function testScrapeImportMatchCategory(){
    $folder = StationFolder::find(1);
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ "name" => $folder->folder_name . "/sdf_".self::$testCategoryCompact."_sfd.mp4", "modified" => Carbon::now(), "size" => 2355, "rev" => "sfaffberr" ]
    ];
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetDir . "sdf_".self::$testCategoryCompact."_sfd_".$files[0]['rev'].".mp4" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());

    $file = UploadedFile::where("path", $expectedOps[1][2])->first();
    $this->assertNotNull($file);
    $this->assertEquals(self::$testCategoryId, $file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
  }

}