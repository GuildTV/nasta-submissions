<?php
namespace Tests\Console;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\StationFolder;
use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;

use App\Mail\Station\EntryFileNoMatch;
use App\Mail\Station\EntryFileCloseDeadline;
use App\Mail\Admin\ExceptionEmail;

use App\Jobs\DropboxDownloadFile;
use App\Jobs\DropboxScrapeMetadata;

use App\Console\Commands\ScrapeUploads;

use Carbon\Carbon;
use Config;
use Mail;
use Queue;

class ScrapeUploadsTest extends TestCase
{
  use DatabaseTransactions;

  private static $dummyHelperClass = "App\Helpers\Files\TestFileServiceHelper";
  private static $debugHelper = false;

  private static $testCategoryId = "no-constraints";
  private static $testCategoryCompact = "no-constraints";

  private static $testCategorySubmittedId = "animation";

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
    Mail::fake();
    Queue::fake();

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
    $targetName = $targetDir . "blah_".$files[0]['hash'].".mp4";
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetName ],
      [ "url", $targetName ],
      // [ "metadata" ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    Mail::assertSent(EntryFileNoMatch::class);
    Mail::assertNotSent(ExceptionEmail::class);
    Queue::assertPushed(DropboxScrapeMetadata::class);
    Queue::assertPushedOn("downloads", DropboxDownloadFile::class);

    $file = UploadedFile::where("path", $targetName)->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
    $this->assertNotNull($file->public_url);
    $this->assertNull($file->metadata);
  }

  public function testScrapeImportMatchCategory(){
    Mail::fake();
    Queue::fake();

    $folder = StationFolder::find(1);
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/station_".self::$testCategoryCompact."_entryname.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];
    $targetName = $targetDir . "station_".self::$testCategoryCompact."_entryname_".$files[0]['hash'].".mp4";
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetName ],
      [ "url", $targetName ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    Mail::assertNotSent(ExceptionEmail::class);
    Queue::assertPushed(DropboxScrapeMetadata::class);
    Queue::assertPushedOn("downloads", DropboxDownloadFile::class);

    $file = UploadedFile::where("path", $targetName)->first();
    $this->assertNotNull($file);
    $this->assertEquals(self::$testCategoryId, $file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
    $this->assertNotNull($file->public_url);
  }

  public function testScrapeImportCategoryFolder(){
    Mail::fake();
    Queue::fake();

    $folder = StationFolder::find(1);
    $folder->category_id = self::$testCategoryId;
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/station_entryname.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];
    $targetName = $targetDir . "station_entryname_".$files[0]['hash'].".mp4";
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetName ],
      [ "url", $targetName ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    Mail::assertNotSent(ExceptionEmail::class);
  }

  public function testScrapeImportCategoryFolderSubmitted(){
    Mail::fake();
    Queue::fake();

    $folder = StationFolder::find(1);
    $folder->category_id = self::$testCategorySubmittedId;
    $targetDir = Config::get('nasta.dropbox_imported_files_path') . "/" . $folder->station->name . "/";

    $files = [
      [ 
        "name" => $folder->folder_name . "/station_entryname.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];
    $targetName = $targetDir . "station_entryname_".$files[0]['hash'].".mp4";
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetName ],
      [ "url", $targetName ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    Mail::assertSent(EntryFileNoMatch::class);
    Mail::assertNotSent(ExceptionEmail::class);
    
    $file = UploadedFile::where("path", $targetName)->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
  }

  public function testScrapeImportMatchNamePrefix(){
    Mail::fake();
    Queue::fake();

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
    $targetName = $targetDir . "blah_entryname_".$files[0]['hash'].".mp4";
    $expectedOps = [
      [ "list", $folder->folder_name ],
      [ "move", $files[0]['name'], $targetName ],
      [ "url", $targetName ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    $this->assertEquals($expectedOps, $helper->getOperations());
    Mail::assertSent(EntryFileNoMatch::class);
    Mail::assertNotSent(ExceptionEmail::class);
    Queue::assertPushed(DropboxScrapeMetadata::class);
    Queue::assertPushedOn("downloads", DropboxDownloadFile::class);

    $file = UploadedFile::where("path", $targetName)->first();
    $this->assertNotNull($file);
    $this->assertNull($file->category_id);
    // $this->assertEquals("blah", $file->name);
    $this->assertEquals($folder->user_id, $file->station_id);
    $this->assertEquals($files[0]['modified'], $file->uploaded_at);
    $this->assertEquals($files[0]['size'], $file->size);
    $this->assertEquals($files[0]['hash'], $file->hash);
    $this->assertNotNull($file->public_url);
  }

  // hits EntryFileCloseDeadline
  public function testScrapeImportEntryCloseDeadline(){
    Mail::fake();
    Queue::fake();

    $cat = Category::create([
      "id" => str_random(10),
      "name" => str_random(10),
      "compact_name" => str_random(10),
      "closing_at" => Carbon::now()->addMinutes(2),
    ]);

    $folder = StationFolder::find(1);

    $files = [
      [ 
        "name" => $folder->folder_name . "/Soem person - blah_" . $cat->compact_name . "_entrymadelate.mp4", 
        "modified" => Carbon::now(), 
        "size" => 2355, 
        "rev" => "sfaffberr",
        "hash" => "hfisisfsd",
      ]
    ];

    $scraper = new ScrapeUploads();
    $helper = new self::$dummyHelperClass($files, self::$debugHelper);

    // check scraper worked as expected
    $res = $scraper->scrapeFolder($helper, $folder);
    $this->assertEquals(null, $res);
    Mail::assertSent(EntryFileCloseDeadline::class);
    Mail::assertNotSent(ExceptionEmail::class);
    Queue::assertPushed(DropboxScrapeMetadata::class);
    Queue::assertPushedOn("downloads", DropboxDownloadFile::class);
  }

}