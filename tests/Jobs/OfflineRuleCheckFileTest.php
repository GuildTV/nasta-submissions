<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileRuleBreak;

use App\Jobs\OfflineRuleCheckFile;

use Carbon\Carbon;

use Exception;
use Config;

class OfflineRuleCheckFileTest extends TestCase
{
  use DatabaseTransactions;

  private static $testCategoryId = "animation";
  private static $testAccountId = "test";

  private function createFile($path=null){
    return UploadedFile::create([
      'station_id' => $this->station->id, 
      'category_id' => self::$testCategoryId,
      'account_id' => self::$testAccountId, 
      'path_local' => ($path == null ? str_random(10) : $path), 
      'path' => 'Test',
      'name' => "Test file", 
      'size' => 9999,
      'hash' => "12345",
      'public_url' => "http://test/test",
      'uploaded_at' => Carbon::now(), 
    ]);
  }

  public function testFileNotFound(){
    $file = $this->createFile();

    $this->assertFalse(file_exists(Config::get('nasta.local_entries_path') . $file->path_local));

    try {
      $job = new OfflineRuleCheckFile($file, false);
      $res = $job->handle();
    } catch (Exception $e){
      return;
    }

    $this->assertFalse(true);
  }

  public function testFileNotLocal(){
    $file = $this->createFile();
    $file->path_local = null;

    $job = new OfflineRuleCheckFile($file, false);
    $res = $job->handle();

    $this->assertEquals("NO_LOCAL", $res);
  }

  public function testFileNoCategory(){
    $file = $this->createFile();
    $file->category_id = null;

    $job = new OfflineRuleCheckFile($file, false);
    $res = $job->handle();

    $this->assertEquals("NO_CATEGORY", $res);
  }

  public function testExistingResult(){
    $file = $this->createFile();

    UploadedFileRuleBreak::create([
      'uploaded_file_id' => $file->id, 
      'result' => 'ok', 
      'metadata' => '{}',
      'mimetype' => 'video/mp4', 
      'length' => 200,
      'warnings' => '[]', 
      'errors' => '[]',
    ]);

    $job = new OfflineRuleCheckFile($file, false);
    $res = $job->handle();

    $this->assertEquals("EXISTING", $res);
  }

  public function testExistingResultOverwrite(){
    $file = $this->createFile("sample.mp4");

    UploadedFileRuleBreak::create([
      'uploaded_file_id' => $file->id, 
      'result' => 'ok', 
      'metadata' => '{}',
      'mimetype' => 'video/mp4', 
      'length' => 200,
      'warnings' => '[]', 
      'errors' => '[]',
    ]);

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();

    $this->assertNotEquals("EXISTING", $res);
  }

  public function testCheckSampleVideo(){
    $file = $this->createFile("sample.mp4");

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();
    $this->assertEquals("NO_SPEC", $res);

    $rule_break = UploadedFileRuleBreak::where('uploaded_file_id', $file->id)->first();
    $this->assertNotNull($rule_break);

    $expected = '{"audio":[],"video":{"format":"AVC","bit_rate":22983.763,"format_profile":"High@L5.1","width":1600,"height":900,"pixel_aspect_ratio":1,"frame_rate":50,"scan_type":"Progressive","standard":"PAL"},"wrapper":"video\/mp4","duration":3.28}';
    $this->assertEquals($expected, $rule_break->metadata);
    $this->assertEquals("video/mp4", $rule_break->mimetype);
    $this->assertEquals(3, $rule_break->length);
    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["resolution"]', $rule_break->errors);
  }

  public function testCheckSamplePng(){
    $file = $this->createFile("test_file.png");

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();
    $this->assertEquals("NON_VIDEO", $res);

    $rule_break = UploadedFileRuleBreak::where('uploaded_file_id', $file->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertEquals('[]', $rule_break->metadata);
    $this->assertEquals("image/png", $rule_break->mimetype);
    $this->assertEquals(-1, $rule_break->length);
    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["bad_mimetype"]', $rule_break->errors);
  }

  public function testCheckSampleTxt(){
    $file = $this->createFile("document.txt");

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();
    $this->assertEquals("NON_VIDEO", $res);

    $rule_break = UploadedFileRuleBreak::where('uploaded_file_id', $file->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertEquals('[]', $rule_break->metadata);
    $this->assertEquals("text/plain", $rule_break->mimetype);
    $this->assertEquals(4, $rule_break->length);
    $this->assertEquals("ok", $rule_break->result);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('[]', $rule_break->errors);
  }

  public function testCheckValidVideo(){
    $file = $this->createFile("valid-sample.mp4");

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();
    $this->assertEquals("OK", $res);

    $rule_break = UploadedFileRuleBreak::where('uploaded_file_id', $file->id)->first();
    $this->assertNotNull($rule_break);

    $expected = '{"audio":{"format":"AAC","bit_rate":192,"maximum_bit_rate":243.375,"channels":2,"sampling_rate":48000},"video":{"format":"AVC","bit_rate":7125.172,"bit_rate_mode":"VBR","maximum_bit_rate":9999.872,"format_profile":"High@L4.1","width":1280,"height":720,"pixel_aspect_ratio":1,"frame_rate":25,"scan_type":"Progressive","standard":"PAL"},"wrapper":"video\/mp4","duration":2.56}';
    $this->assertEquals($expected, $rule_break->metadata);
    $this->assertEquals("video/mp4", $rule_break->mimetype);
    $this->assertEquals(3, $rule_break->length);
    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["video.bit_rate"]', $rule_break->errors);
  }

  public function testNewEntry(){
    $file = $this->createFile("valid-sample.mp4");

    $old_break = UploadedFileRuleBreak::create([
      'uploaded_file_id' => $file->id, 
      'result' => 'ok', 
      'metadata' => '{}',
      'mimetype' => 'video/mp4', 
      'length' => 200,
      'warnings' => '[]', 
      'errors' => '[]',
    ]);

    $job = new OfflineRuleCheckFile($file, true);
    $res = $job->handle();

    $rule_break = UploadedFileRuleBreak::where('uploaded_file_id', $file->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertNotEquals($old_break->id, $rule_break->id);
  }

}