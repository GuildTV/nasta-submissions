<?php
namespace Tests\Http\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\User;
use App\Database\Entry\Entry;
use App\Database\Category\Category;

class EntryControllerTest extends TestCase
{
  // use DatabaseTransactions;

  private static $testCategory = "animation";
  private static $testClosedCategory = "something";
  private static $submitUrl = '/station/categories/animation/submit';
  private static $submitClosedUrl = '/station/categories/something/submit';
  private static $submitEditUrl = '/station/categories/animation/edit';
  private static $submitEditClosedUrl = '/station/categories/something/edit';

  private function assertEntry($entry, $expected){
    $this->assertNotNull($entry);

    if (isset($expected['name']))
      $this->assertEquals($expected['name'], $entry->name);
    if (isset($expected['description']))
      $this->assertEquals($expected['description'], $entry->description);
    if (isset($expected['rules']))
      $this->assertEquals($expected['rules'], $entry->rules ? 1 : 0);

    if (isset($expected['submit']))
      $this->assertEquals($expected['submit'], $entry->submitted ? 1 : 0);
    if (isset($expected['submitted']))
      $this->assertEquals($expected['submitted'], $entry->submitted ? 1 : 0);
  }

  private function assertSave($data, $url, $category){
    $this->actingAs($this->station)->postAjax($url, $data)
      ->assertResponseOk();

    $entry = Entry::where('category_id', $category)
      ->where('station_id', $this->station->id)
      ->first();

    $this->assertEntry($entry, $data);
  }

  private function entryExists($category){
    return Entry::where('category_id', $category)
      ->where('station_id', $this->station->id)
      ->count() > 0;
  }


  public function testAuthorization()
  {
    $this->postAjax(self::$submitUrl)
        ->assertResponseStatus(401);

    $this->actingAs($this->station)->postAjax(self::$submitUrl)
        ->assertResponseStatus(422);

    $this->actingAs($this->judge)->postAjax(self::$submitUrl)
        ->assertResponseStatus(403);

    $this->actingAs($this->admin)->postAjax(self::$submitUrl)
        ->assertResponseStatus(403);
  }

  public function testInitialSave(){
    // ensure there is no entry already
    Entry::where('category_id', self::$testCategory)
      ->where('station_id', $this->station->id)
      ->delete();

    $data = [
      'name' => "Test submission",
      'description' => "Something exciting about pillows.",
      'rules' => 1,
      'submit' => 0
    ];

    $this->assertSave($data, self::$submitUrl, self::$testCategory);
  }

  public function testInitialSaveClosed(){
    // ensure there is no entry already
    Entry::where('category_id', self::$testClosedCategory)
      ->where('station_id', $this->station->id)
      ->delete();

    $data = [
      'name' => "Test submission",
      'description' => "Something exciting about pillows.",
      'rules' => 1,
      'submit' => 0
    ];

    $this->actingAs($this->station)->postAjax(self::$submitClosedUrl, $data)
      ->assertResponseStatus(400);

    $this->assertFalse($this->entryExists(self::$testClosedCategory));
  }

  public function testUpdate(){

    $data = [
      'name' => "Test submission 2",
      'description' => "Something exciting about pillows. 2",
      'rules' => 0,
      'submit' => 1
    ];

    $this->assertSave($data, self::$submitUrl, self::$testCategory);
  }

  public function testUpdateClosed(){
    $origData = [
      'name' => "Test submission",
      'description' => "Something exciting about pillows.",
      'rules' => 1,
      'submit' => 0
    ];
    // need to manually assign some as fillable is not set
    $entry = new Entry($origData);
    $entry->category_id = self::$testClosedCategory;
    $entry->station_id = $this->station->id;
    $entry->rules = 1;
    $entry->submitted = 0;
    $entry->save();

    $data = [
      'name' => "Test submission 2",
      'description' => "Something exciting about pillows. 2",
      'rules' => 0,
      'submit' => 1
    ];

    $this->actingAs($this->station)->postAjax(self::$submitClosedUrl, $data)
      ->assertResponseStatus(400);

    $entry = Entry::where('category_id', self::$testClosedCategory)
      ->where('station_id', $this->station->id)
      ->first();

    // still matches original
    $this->assertEntry($entry, $origData);
  }

  public function testEnableEdit(){
    $this->assertSave([], self::$submitEditUrl, self::$testCategory);

    $entry = Entry::where('category_id', self::$testCategory)
      ->where('station_id', $this->station->id)
      ->first();

    $this->assertEquals(0, $entry->submitted);
  }

  public function testEnableEditClosed(){
    $entry = Entry::where('category_id', self::$testClosedCategory)
      ->where('station_id', $this->station->id)
      ->first();
    $this->assertNotNull($entry);

    $entry->submitted = true;
    $entry->save();

    $this->actingAs($this->station)->postAjax(self::$submitEditClosedUrl, [])
      ->assertResponseStatus(400);

    $entry = Entry::where('category_id', self::$testClosedCategory)
      ->where('station_id', $this->station->id)
      ->first();

    $this->assertEquals(1, $entry->submitted);
  }

}