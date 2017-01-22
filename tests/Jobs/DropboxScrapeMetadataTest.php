<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\DropboxAccount;

use App\Helpers\Files\DropboxFileServiceHelper;
use App\Helpers\Files\TestFileServiceHelper;
use App\Jobs\DropboxScrapeMetadata;

use Carbon\Carbon;

use Exception;
use Config;

class DropboxScrapeMetadataTest extends TestCase
{
  use DatabaseTransactions;

  private static $debugHelper = false;
  private static $testAccountId = "test";
  private static $testSourceTextFile = "/test/document.txt";
  private static $testSourceVideoFile = "/test/sample.mp4";

  public function testFileMissing(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $client = new DropboxFileServiceHelper($file->account->access_token);
    $this->assertFalse($client->fileExists($file->path));

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("API_FAIL", $job->handle());
  }

  public function testResponsePending(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $files = [
      [ 
        "name" => $file->path,
        "metadata" => [
          "pending" => true 
        ]
      ]
    ];

    $client = new TestFileServiceHelper($files, self::$debugHelper);

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("PARSE_FAIL", $job->handle());
  }

  public function testResponseNotVideo(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $files = [
      [ 
        "name" => $file->path,
        "metadata" => [
          "metadata" => [
            ".tag" => "photo"
          ]
        ]
      ]
    ];

    $client = new TestFileServiceHelper($files, self::$debugHelper);

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("PARSE_FAIL", $job->handle());
  }

  public function testResponseNoMediaInfo(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $files = [
      [ 
        "name" => $file->path,
        "metadata" => [ "me" => "OK" ]
      ]
    ];

    $client = new TestFileServiceHelper($files, self::$debugHelper);

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("PARSE_FAIL", $job->handle());
  }

  public function testResponseOK(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $files = [
      [ 
        "name" => $file->path,
        "metadata" => [
          "metadata" => [
            ".tag" => "video",
            "dimensions" => [
              "width" => 1280,
              "height" => 720,
            ],
            "duration" => 1200,
          ]
        ]
      ]
    ];

    $client = new TestFileServiceHelper($files, self::$debugHelper);

    $job = new DropboxScrapeMetadata($file, $client);
    $res = $job->handle();
    $this->assertTrue(is_array($res));
    $this->assertEquals(1280, $res['width']);
    $this->assertEquals(720, $res['height']);
    $this->assertEquals(1200, $res['duration']);
  }

  public function testScrapeTextFile(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => "/" . str_random(15) . ".tmp",
      'name' => 'Test file for scraping',
      'uploaded_at' => Carbon::now(),
    ]);

    $client = new DropboxFileServiceHelper($file->account->access_token);
    $this->assertTrue($client->ensureFileExists(storage_path().self::$testSourceTextFile, $file->path));

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("API_FAIL", $job->handle());
  }

  public function testScrapeVideoFile(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => "/" . str_random(15) . ".mp4",
      'name' => 'Test file for scraping',
      'uploaded_at' => Carbon::now(),
    ]);

    $client = new DropboxFileServiceHelper($file->account->access_token);
    $this->assertTrue($client->ensureFileExists(storage_path().self::$testSourceVideoFile, $file->path));

    $job = new DropboxScrapeMetadata($file, $client);
    $this->assertEquals("PARSE_FAIL", $job->handle());
  }

  public function testScrapeVideoFileWithMetadata(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => "/DO_NOT_REMOVE.mp4",
      'name' => 'Test file for scraping',
      'uploaded_at' => Carbon::now(),
    ]);

    $client = new DropboxFileServiceHelper($file->account->access_token);
    $this->assertTrue($client->fileExists($file->path));

    $job = new DropboxScrapeMetadata($file, $client);
    $res = $job->handle();
    $this->assertTrue(is_array($res));
    $this->assertEquals(1600, $res['width']);
    $this->assertEquals(900, $res['height']);
    $this->assertEquals(3280, $res['duration']);

    $file = UploadedFile::find($file->id);
    $this->assertNotNull($file->metadata);
    $this->assertEquals($res['width'], $file->metadata->width);
    $this->assertEquals($res['height'], $file->metadata->height);
    $this->assertEquals($res['duration'], $file->metadata->duration);
  }

}