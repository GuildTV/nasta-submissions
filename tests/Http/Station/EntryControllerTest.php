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
  private static $submitUrl = '/station/categories/animation/submit';

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

  private function assertSave($data){
    $this->actingAs($this->station)->postAjax(self::$submitUrl, $data)
      ->assertResponseOk();

    $entry = Entry::where('category_id', self::$testCategory)
      ->where('station_id', $this->station->id)
      ->first();

    $this->assertEntry($entry, $data);
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

    $this->assertSave($data);
  }


}