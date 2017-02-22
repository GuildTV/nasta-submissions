<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\UploadedFile;
use App\Database\Encode\EncodeJob;
use App\Database\Encode\EncodeWatch;

use App\Jobs\Encode\QueueEncode;

class QueueEncodeTest extends TestCase
{
  use DatabaseTransactions;

  public function testFileMissing(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $file->path_local = null;
    $this->assertNull($file->path_local);
    $job = new QueueEncode($file, 99);
    
    $res = $job->handle();
    $this->assertFalse($res);
  }

  public function testQueue(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);
    $file->path_local = "fake/path.mp4";
    $file->save();
    $expected = "fake/path-fixed.mp4";

    // ensure no false positives
    $watch = $file->transcode;
    $this->assertNotNull($watch);
    $encJob = $watch->job;
    $this->assertNotNull($encJob);
    $watch->delete();
    $encJob->delete();

    // run job
    $job = new QueueEncode($file, 99);
    $res = $job->handle();
    $this->assertNotFalse($res);

    $encJob = EncodeJob::first();
    $this->assertNotNull($encJob);
    $this->assertEquals($res->id, $encJob->id);
    $this->assertEquals($file->path_local, $encJob->source_file);
    $this->assertEquals($expected, $encJob->destination_file);
    $this->assertEquals(99, $encJob->format_id);
    $this->assertEquals("Not Encoding", $encJob->status);
    $this->assertEquals(0, $encJob->progress);

    $watch = EncodeWatch::first();
    $this->assertNotNull($watch);
    $this->assertEquals($file->id, $watch->uploaded_file_id);
    $this->assertEquals($encJob->id, $watch->job_id);
  }

}