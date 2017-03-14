<?php
namespace Tests\Http\Judge;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Mail\Judge\CategoryJudged;

use App\Database\Entry\Entry;
use App\Database\Entry\EntryResult;
use App\Database\Entry\EntryRuleBreak;
use App\Database\Category\Category;

use Mail;

class JudgeControllerFinalizeTest extends TestCase
{
  use DatabaseTransactions;

  private static $finalizeUrl = '/judge/finalize/animation';
  private static $categoryId = 'animation';

  private function assertResult($result, $expected){
    $this->assertNotNull($result);

    if (isset($expected['winner_id']))
      $this->assertEquals($expected['winner_id'], $result->winner_id);
    if (isset($expected['winner_comment']))
      $this->assertEquals($expected['winner_comment'], $result->winner_comment);
    if (isset($expected['commended_id']))
      $this->assertEquals($expected['commended_id'], $result->commended_id);
    if (isset($expected['commended_comment']))
      $this->assertEquals($expected['commended_comment'], $result->commended_comment);
  }

  private function assertFinalize($url, $data, $expected=null){
    $this->actingAs($this->judge)->postAjax($url, $data)
      ->assertResponseOk();

    $category = Category::find(self::$categoryId);
    $this->assertResult($category->result, $expected==null?$data:$expected);

    return $category->result;
  }

  private function assertFinalizeFail($url, $data, $expected=null, $status=422){
    $this->actingAs($this->judge)->postAjax($url, $data)
      ->assertResponseStatus($status);

    $category = Category::find(self::$categoryId);

    if ($expected == null) {
      $this->assertNull($category->result);
    } else {
      $this->assertResult($category->result, $expected);
    }

    return $category->result;
  }

  private function createEntryResult($entryId, $score=null){
    return EntryResult::create([
      'entry_id' => $entryId,
      'score' => $score==null ? 10 : $score,
      'feedback' => 'Some initial feedback text',
    ]);
  }

  public function testAuthorization()
  {
    $this->postAjax(self::$finalizeUrl)
        ->assertResponseStatus(401);

    $this->actingAs($this->station)->postAjax(self::$finalizeUrl)
        ->assertResponseStatus(403);

    $this->actingAs($this->judge)->postAjax(self::$finalizeUrl)
        ->assertResponseStatus(422);

    $this->actingAs($this->admin)->postAjax(self::$finalizeUrl)
        ->assertResponseStatus(403);
  }

  public function testFinalize(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    Mail::fake();

    $this->assertFinalize(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ]);

    Mail::assertSent(CategoryJudged::class);
  }
  public function testFinalizeNoComments(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $this->assertFinalize(self::$finalizeUrl, [
      'winner_id' => 29,
      'commended_id' => 75,
    ], [
      'winner_id' => 29,
      'winner_comment' => "",
      'commended_id' => 75,
      'commended_comment' => "",
    ]);
  }
  public function testFinalizeSameIds(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 29,
      'commended_comment' => str_random(30),
    ]);
  }
  public function testFinalizeEntryAnotherCategory(){
    // entry from another category
    $entry = Entry::find(72);
    $this->assertNotNull($entry);
    $this->createEntryResult(72, 19);

    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 72,
      'winner_comment' => str_random(30),
      'commended_id' => 29,
      'commended_comment' => str_random(30),
    ]);
  }
  public function testFinalizeEntryInvalidId(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 1,
      'winner_comment' => str_random(30),
      'commended_id' => 29,
      'commended_comment' => str_random(30),
    ]);
  }
  public function testFinalizeEntryNotEnoughIds(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
    ]);
  }
  public function testFinalizeEntryMissingResult(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $e = Entry::create([
      'category_id' => self::$categoryId,
      'station_id' => $this->admin->id,
      'name' => str_random(40),
    ]);
    $e->submitted = true;
    $e->rules = true;
    $e->save();
    EntryRuleBreak::create([
      'entry_id' => $e->id,
      'result' => 'ok'
    ]);

    $this->assertTrue($e->canBeJudged());

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ]);
  }
  public function testFinalizeEntryIgnoreNoRuleBreak(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    Entry::create([
      'category_id' => self::$categoryId,
      'station_id' => $this->admin->id,
      'name' => str_random(40),
    ]);

    $this->assertFinalize(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ]);
  }
  public function testFinalizeOnlyOneEntry(){
    $this->createEntryResult(29, 20);

    Entry::find(75)->delete();

    $this->assertFinalize(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
    ]);
  }
  public function testFinalizeNoEntry(){
    Entry::find(75)->delete();
    Entry::find(29)->delete();

    $this->assertFinalizeFail(self::$finalizeUrl, []);
  }
  public function testFinalizeAlreadyFinalised(){
    $this->createEntryResult(29, 20);
    $this->createEntryResult(75, 10);

    $res = $this->assertFinalize(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ]);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ], $res, 403);
  }
  public function testFinalizeDuplicateWinnerScore(){
    $this->createEntryResult(29, 10);
    $this->createEntryResult(75, 10);

    $this->assertFinalizeFail(self::$finalizeUrl, [
      'winner_id' => 29,
      'winner_comment' => str_random(30),
      'commended_id' => 75,
      'commended_comment' => str_random(30),
    ]);
  }

}