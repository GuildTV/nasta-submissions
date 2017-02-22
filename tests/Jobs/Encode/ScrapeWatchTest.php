<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;
use Illuminate\Support\Facades\Queue;

use App\Database\Upload\UploadedFile;
use App\Database\Encode\EncodeJob;
use App\Database\Encode\EncodeWatch;

use App\Jobs\Encode\ScrapeWatch;
use App\Jobs\Encode\UploadEncoded;

class ScrapeWatchTest extends TestCase
{
  use DatabaseTransactions;

  private static $debugHelper = false;
  private static $testAccountId = "test";
  private static $testSourceTextFile = "/test/document.txt";
  private static $testSourceImageFile = "/test/test_file.png";

  public function testSuccess(){
    Queue::fake();

    $this->assertEquals(1, EncodeJob::count());
    $this->assertEquals(1, EncodeWatch::count());

    $job = new ScrapeWatch();
    $res = $job->handle();
    $this->assertEquals(1, $res);

    $this->assertEquals(1, EncodeJob::count());
    $this->assertEquals(0, EncodeWatch::count());

    Queue::assertPushedOn('downloads', UploadEncoded::class);
  }

  public function testNotFinished(){
    Queue::fake();

    $this->assertEquals(1, EncodeWatch::count());
    $encJob = EncodeJob::first();
    $this->assertNotNull($encJob);

    $encJob->status = "Moving File";
    $encJob->save();

    $job = new ScrapeWatch();
    $res = $job->handle();
    $this->assertEquals(0, $res);

    $this->assertEquals(1, EncodeJob::count());
    $this->assertEquals(1, EncodeWatch::count());

    Queue::assertNotPushed(UploadEncoded::class);
  }

  public function testMissingJob(){
    Queue::fake();

    $this->assertEquals(1, EncodeWatch::count());
    $encJob = EncodeJob::first();
    $this->assertNotNull($encJob);
    $encJob->delete();

    $job = new ScrapeWatch();
    $res = $job->handle();
    $this->assertEquals(0, $res);

    $this->assertEquals(0, EncodeJob::count());
    $this->assertEquals(0, EncodeWatch::count());

    Queue::assertNotPushed(UploadEncoded::class);
  }

  // public function testQueue(){
  //   $file = UploadedFile::find(131);
  //   $this->assertNotNull($file);
  //   $file->path_local = "fake/path.mp4";
  //   $expected = "fake/path-fixed.mp4";

  //   // ensure no false positives
  //   EncodeJob::truncate();
  //   EncodeWatch::truncate();

  //   // run job
  //   $job = new QueueEncode($file, 99);
  //   $res = $job->handle();
  //   $this->assertNotNull($res);

  //   $encJob = EncodeJob::first();
  //   $this->assertNotNull($encJob);
  //   $this->assertEquals($file->path_local, $encJob->source_file);
  //   $this->assertEquals($expected, $encJob->destination_file);
  //   $this->assertEquals(99, $encJob->format_id);
  //   $this->assertEquals("Not Encoding", $encJob->status);
  //   $this->assertEquals(0, $encJob->progress);

  //   $watch = EncodeWatch::first();
  //   $this->assertNotNull($watch);
  //   $this->assertEquals($file->id, $watch->uploaded_file_id);
  //   $this->assertEquals($encJob->id, $watch->job_id);
  // }

}