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

}