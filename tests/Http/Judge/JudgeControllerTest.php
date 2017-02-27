<?php
namespace Tests\Http\Judge;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;

use Hash;

class JudgeControllerTest extends TestCase
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

  private function assertScore($data, $expected=null){
    $this->actingAs($this->judge)->postAjax(self::$scoreUrl, $data)
      ->assertResponseOk();

    $entry = Entry::find(self::$entryId);
    $this->assertResult($entry->result, $expected==null?$data:$expected);

    return $entry->result;
  }

  private function assertScoreFail($data, $expected=null, $status=422){
    $this->actingAs($this->judge)->postAjax(self::$scoreUrl, $data)
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
    $this->assertScore([
      'score' => 20,
      'feedback' => str_random(30),
    ]);
  }
  public function testScoreNoFeedback(){ 
    $this->assertScore([
      'score' => 20,
    ], [
      'score' => 20,
      'feedback' => "",
    ]);
  }

  public function testScoreBadScore(){
    $this->assertScoreFail([
      'score' => -1,
    ]);
    $this->assertScoreFail([
      'score' => 21,
    ]);
    $this->assertScoreFail([
      'score' => 'err',
    ]);
  }

  public function testScoreNoScore(){
    $this->assertScoreFail([
      'feedback' => str_random(30),
    ]);
  }

  public function testScoreUpdateClearFeedback(){
    $this->createEntryResult();
    $this->assertScore([
      'score' => 5,
    ], [
      'score' => 5,
      'feedback' => "",
    ]);
  }
  public function testScoreUpdateWithFeedback(){
    $this->createEntryResult();
    $this->assertScore([
      'score' => 5,
      'feedback' => str_random(50),
    ]);
  }

}