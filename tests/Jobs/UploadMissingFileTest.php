<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;
use Illuminate\Support\Facades\Queue;

use App\Database\Upload\UploadedFile;
use App\Database\Category\Category;

use App\Jobs\UploadMissingFile;

use Exception;

class UploadMissingFileTest extends TestCase
{
  use DatabaseTransactions;

  private static $dummyHelperClass = "App\Helpers\Files\TestFileServiceHelper";
  private static $debugHelper = false;

  public function testUploadMissingFile(){
    Queue::fake();

    $helper = new self::$dummyHelperClass([], self::$debugHelper);

    $path = "not real file";
    $category = Category::first();

    try {
      $res = (new UploadMissingFile($path, $category, $this->station, $helper))->handle();
    } catch (Exception $e){
      return;
    }

    // should stop in exception handler
    $this->assertTrue(false);
  }

  public function testUploadSuccess(){
    Queue::fake();

    $helper = new self::$dummyHelperClass([], self::$debugHelper);

    $path = "valid-sample.mp4";
    $target = "/Imported/Test Station/valid-sample-manual.mp4";
    $category = Category::first();

    $expectedOps = [
      [ "upload", env('LOCAL_ENTRY_DIR').$path, $target ],
      [ "url", $target]
    ];

    $res = (new UploadMissingFile($path, $category, $this->station, $helper))->handle();
    $this->assertNotNull($res);
    $this->assertEquals($expectedOps, $helper->getOperations());

    $uploadOps = $helper->getUploadedOperations();
    $this->assertEquals(1, count($uploadOps));

    $this->assertEquals($this->station->id, $res->station_id);
    $this->assertNotNull($res->account_id);
    $this->assertEquals($target, $res->path);
    $this->assertEquals($path, $res->path_local);
    $this->assertEquals($uploadOps[$target]['name'], $res->name);
    $this->assertEquals($uploadOps[$target]['size'], $res->size);
    $this->assertEquals($uploadOps[$target]['hash'], $res->hash);
    $this->assertNotNull($res->public_url);
    $this->assertEquals($category->id, $res->category_id);
    $this->assertEquals($uploadOps[$target]['modified'], $res->uploaded_at);

    // Queued jobs
    Queue::assertNotPushed(\App\Jobs\DropboxDownloadFile::class);
    Queue::assertPushed(\App\Jobs\DropboxScrapeMetadata::class);
    Queue::assertPushedOn('downloads', \App\Jobs\OfflineRuleCheckFile::class);
  }

}