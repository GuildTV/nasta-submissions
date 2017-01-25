<?php
namespace Tests\Http\Station;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\User;
use App\Database\Entry\Entry;
use App\Database\Category\Category;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\DropboxAccount;

use App\Helpers\Files\DropboxFileServiceHelper;

use Carbon\Carbon;

class FileControllerTest extends TestCase
{
  use DatabaseTransactions;

  private static $testCategory = "no-constraints";
  private static $testSubmittedCategory = "animation";
  private static $testClosedCategory = "something";

  private static $deleteUrl = '/station/files/%d/delete';
  private static $linkUrl = '/station/files/%d/link/%s';

  private static $testAccountId = "test";
  private static $testSourceFile = "/test/test_file.png";

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
    $url = sprintf(self::$deleteUrl, 10);

    $this->postAjax($url)
        ->assertResponseStatus(401);

    $this->actingAs($this->station)->postAjax($url)
        ->assertResponseStatus(404);

    $this->actingAs($this->judge)->postAjax($url)
        ->assertResponseStatus(404);

    $this->actingAs($this->admin)->postAjax($url)
        ->assertResponseStatus(404);
  }

  public function testDelete(){
    $filename = "/" . str_random(15) . ".tmp";

    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => self::$testCategory,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists(storage_path().self::$testSourceFile, $filename);

    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, $file->id), [])
      ->assertResponseOk();

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertNull($file);

    $this->assertFalse($dropbox->fileExists($filename));
  }

  public function testDeleteClosed(){
    $filename = "/" . str_random(15) . ".tmp";

    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => self::$testClosedCategory,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists(storage_path().self::$testSourceFile, $filename);

    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, $file->id), [])
      ->assertResponseStatus(400);

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertNotNull($file);

    $this->assertTrue($dropbox->fileExists($filename));
  }

  public function testDeleteSubmitted(){
    $filename = "/" . str_random(15) . ".tmp";

    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => self::$testSubmittedCategory,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists(storage_path().self::$testSourceFile, $filename);

    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, $file->id), [])
      ->assertResponseStatus(400);

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertNotNull($file);

    $this->assertTrue($dropbox->fileExists($filename));
  }

  public function testDeleteMissingDropbox(){
    $filename = "/" . str_random(15) . ".tmp";

    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => self::$testCategory,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $this->assertFalse($dropbox->fileExists($filename));

    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, $file->id), [])
      ->assertResponseOk();

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertNull($file);
  }

  public function testDeleteClosedMissingDropbox(){
    $filename = "/" . str_random(15) . ".tmp";

    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => self::$testClosedCategory,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $this->assertFalse($dropbox->fileExists($filename));

    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, $file->id), [])
      ->assertResponseStatus(400);

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertNotNull($file);
  }

  public function testDeleteNotFound(){
    $this->actingAs($this->station)->postAjax(sprintf(self::$deleteUrl, 9999999), [])
      ->assertResponseStatus(404);
  }

  public function testLinkBadCategory(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => '/dummy-file',
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $this->actingAs($this->station)->postAjax(sprintf(self::$linkUrl, $file->id, "notacategory"), [])
      ->assertResponseStatus(404);
  }

  public function testLinkClosedCategory(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => '/dummy-file',
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $this->actingAs($this->station)->postAjax(sprintf(self::$linkUrl, $file->id, self::$testClosedCategory), [])
      ->assertResponseStatus(400);
  }

  public function testLinkBadId(){
    $this->actingAs($this->station)->postAjax(sprintf(self::$linkUrl, 9999999, "animation"), [])
      ->assertResponseStatus(404);
  }

  public function testLinkGood(){
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => '/dummy-file',
      'name' => 'Test file for delete',
      'uploaded_at' => Carbon::now(),
    ]);

    $this->actingAs($this->station)->postAjax(sprintf(self::$linkUrl, $file->id, self::$testCategory), [])
      ->assertResponseOk();

    $file = UploadedFile::where("id", $file->id)->first();
    $this->assertEquals(self::$testCategory, $file->category_id);
  }

}
