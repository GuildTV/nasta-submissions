<?php
namespace Tests\Http\Judge;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;
use App\Database\Entry\EntryRuleBreak;
use App\Database\Category\CategoryResult;

use Hash;

class JudgeControllerScoreTest extends TestCase
{
  use DatabaseTransactions;

  private static $scoreUrl = '/judge/entry/29/score';
  private static $entryId = 29;

  private function assertResult($result, $expected){
    $this->assertNotNull($result);

    if (isset($expected['score']))
      $this->assertEquals($expected['score'], $result->score);
    if (isset($expected['feedback']))
      $this->assertEquals($expected['feedback'], $result->feedback);
  }

  private function assertScore($url, $data, $expected=null){
    $this->actingAs($this->judge)->postAjax($url, $data)
      ->assertResponseOk();

    $entry = Entry::find(self::$entryId);
    $this->assertResult($entry->result, $expected==null?$data:$expected);

    return $entry->result;
  }

  private function assertScoreFail($url, $data, $expected=null, $status=422){
    $this->actingAs($this->judge)->postAjax($url, $data)
      ->assertResponseStatus($status);

    $entry = Entry::find(self::$entryId);

    if ($expected == null) {
      $this->assertNull($entry->result);
    } else {
      $this->assertResult($entry->result, $expected);
    }

    return $entry->result;
  }

  private function createEntryResult(){
    return EntryResult::create([
      'entry_id' => self::$entryId,
      'score' => 1,
      'feedback' => 'Some initial feedback text',
    ]);
  }


  public function testAuthorization()
  {
    $this->postAjax(self::$scoreUrl)
        ->assertResponseStatus(401);

    $this->actingAs($this->station)->postAjax(self::$scoreUrl)
        ->assertResponseStatus(403);

    $this->actingAs($this->judge)->postAjax(self::$scoreUrl)
        ->assertResponseStatus(422);

    $this->actingAs($this->admin)->postAjax(self::$scoreUrl)
        ->assertResponseStatus(403);
  }

  public function testScore(){
    $this->assertScore(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ]);
  }
  public function testScoreNoFeedback(){ 
    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
    ]);
  }

  public function testScoreBadScore(){
    $this->assertScoreFail(self::$scoreUrl, [
      'score' => -1,
    ]);
    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 21,
    ]);
    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 'err',
    ]);
  }

  public function testScoreNoScore(){
    $this->assertScoreFail(self::$scoreUrl, [
      'feedback' => str_random(30),
    ]);
  }

  public function testScoreUpdateClearFeedback(){
    $res = $this->createEntryResult();
    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 5,
    ], $res);
  }
  public function testScoreUpdateWithFeedback(){
    $this->createEntryResult();
    $this->assertScore(self::$scoreUrl, [
      'score' => 5,
      'feedback' => str_random(50),
    ]);
  }

  public function testScoreNoRuleBreak(){
    EntryRuleBreak::where('entry_id', self::$entryId)->delete();

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }
  public function testScoreRuleBreakUnknown(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "unknown";
    $result->save();

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }
  public function testScoreRuleBreakBreak(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "break";
    $result->save();

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }
  public function testScoreRuleWarning(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "warning";
    $result->save();

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }
  public function testScoreRuleBreakOk(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "ok";
    $result->save();

    $this->assertScore(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ]);
  }
  public function testScoreRuleBreakAccepted(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "accepted";
    $result->save();

    $this->assertScore(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ]);
  }
  public function testScoreRuleBreakRejected(){
    $result = EntryRuleBreak::where('entry_id', self::$entryId)->first();
    $result->result = "rejected";
    $result->save();

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }

  public function testScoreFinalized(){
    $entry = Entry::find(self::$entryId);
    $this->assertNotNull($entry);

    CategoryResult::create([
      'category_id' => $entry->category_id,
    ]);

    $this->assertScoreFail(self::$scoreUrl, [
      'score' => 20,
      'feedback' => str_random(30),
    ], null, 403);
  }

}