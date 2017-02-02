<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileRuleBreak;
use App\Database\Entry\Entry;
use App\Database\Entry\EntryRuleBreak;

use App\Jobs\OfflineRuleCheckEntry;

use Carbon\Carbon;

use Exception;
use Config;

class OfflineRuleCheckEntryTest extends TestCase
{
  use DatabaseTransactions;

  public function testNotSubmitted(){
    $entry = Entry::find(72);
    $this->assertNotNull($entry);
    $entry->submitted = false;

    $job = new OfflineRuleCheckEntry($entry);
    $res = $job->handle();

    $this->assertEquals("SUBMITTED", $res);
  }

  public function testNotSubmittedForce(){
    $entry = Entry::find(72);
    $this->assertNotNull($entry);
    $entry->submitted = false;

    $job = new OfflineRuleCheckEntry($entry, true);
    $res = $job->handle();

    $this->assertNotEquals("SUBMITTED", $res);
  }

  public function testRunExistingEntry(){
    $entry = Entry::find(29);
    $this->assertNotNull($entry);

    $job = new OfflineRuleCheckEntry($entry, true);
    $res = $job->handle();

    $this->assertEquals("OK", $res);

    $rule_break = EntryRuleBreak::where('entry_id', $entry->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals('{"193":2,"20":1}', $rule_break->constraint_map);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["file_too_long=193","file_too_long=20","file_result.break=20"]', $rule_break->errors);
  }

  public function testRunNoFilesEntry(){
    $entry = Entry::find(29);
    $this->assertNotNull($entry);

    UploadedFile::where('category_id', $entry->category_id)->where('station_id', $entry->station_id)->delete();

    $job = new OfflineRuleCheckEntry($entry, true);
    $res = $job->handle();

    $this->assertEquals("OK", $res);

    $rule_break = EntryRuleBreak::where('entry_id', $entry->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals("[]", $rule_break->constraint_map);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["file_count","matched_file_count"]', $rule_break->errors);
  }

  public function testRunMatchingFilesEntry(){
    $entry = Entry::find(29);
    $this->assertNotNull($entry);

    UploadedFile::where('category_id', $entry->category_id)
      ->where('station_id', $entry->station_id)
      ->where('id', '!=', 20)
      ->delete();

    $job = new OfflineRuleCheckEntry($entry, true);
    $res = $job->handle();

    $this->assertEquals("OK", $res);

    $rule_break = EntryRuleBreak::where('entry_id', $entry->id)->first();
    $this->assertNotNull($rule_break);

    $this->assertEquals("break", $rule_break->result);
    $this->assertEquals('{"20":1}', $rule_break->constraint_map);
    $this->assertEquals("[]", $rule_break->warnings);
    $this->assertEquals('["file_count","matched_file_count","file_too_long=20","file_result.break=20"]', $rule_break->errors);
  }




}