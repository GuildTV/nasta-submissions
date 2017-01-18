<?php
namespace Tests\Jobs;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\UploadedFile;
use App\Database\Upload\DropboxAccount;

use App\Helpers\Files\DropboxFileServiceHelper;
use App\Jobs\DropboxDownloadFile;

use Carbon\Carbon;

use Exception;
use Config;

class DropboxDownloadFileTest extends TestCase
{
  use DatabaseTransactions;

  private static $testAccountId = "test";
  private static $testSourceFile = "/test/test_file.png";
  private static $testSourceHash = "4ca754f93a50a3b7e97716fe25e01ccecb5735420ff908429f840bd11c262be7";

  public function testAlreadyDownloadedFile(){
    $file = UploadedFile::find(131);
    $this->assertNotNull($file);

    $job = new DropboxDownloadFile($file);
    $this->assertFalse($job->handle());
  }

  public function testDropboxFileNotFound(){
    $file = UploadedFile::find(22);
    $this->assertNotNull($file);

    $job = new DropboxDownloadFile($file);

    try {
      $job->handle();
      $this->assertTrue(false);
    } catch (Exception $e) {
    }
  }

  // test missing/bad size
  public function testFileBadSize(){
    $filename = "/" . str_random(15) . ".tmp";
    $srcFile = storage_path().self::$testSourceFile;

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists($srcFile, $filename);

    // create uploaded file entry
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'size' => 9999999999,
      'hash' => self::$testSourceHash,
      'uploaded_at' => Carbon::now(),
    ]);
    $this->assertNull($file->path_local);

    // run the download
    $job = new DropboxDownloadFile($file);

    try {
      $job->handle();
      $this->assertTrue(false);
    } catch (Exception $e) {
    }
  }

  // test missing/bad hash
  public function testFileBadHash(){
    $filename = "/" . str_random(15) . ".tmp";
    $srcFile = storage_path().self::$testSourceFile;

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists($srcFile, $filename);

    // create uploaded file entry
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete',
      'size' => filesize($srcFile),
      'hash' => 'sfjrgwrgr',
      'uploaded_at' => Carbon::now(),
    ]);
    $this->assertNull($file->path_local);

    // run the download
    $job = new DropboxDownloadFile($file);

    try {
      $job->handle();
      $this->assertTrue(false);
    } catch (Exception $e) {
    }
  }

  // test good
  public function testSuccess(){
    $filename = "/" . str_random(15) . ".tmp";
    $srcFile = storage_path().self::$testSourceFile;

    $account = DropboxAccount::where("id", self::$testAccountId)->first();

    // ensure file exists on dropbox
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $dropbox->ensureFileExists($srcFile, $filename);

    // create uploaded file entry
    $file = UploadedFile::create([
      'station_id' => $this->station->id,
      'category_id' => null,
      'account_id' => self::$testAccountId,
      'path' => $filename,
      'name' => 'Test file for delete.tmp',
      'hash' => self::$testSourceHash,
      'size' => filesize($srcFile),
      'uploaded_at' => Carbon::now(),
    ]);
    $this->assertNull($file->path_local);

    // run the download
    $job = new DropboxDownloadFile($file);

    $job->handle();

    // ensure file exists
    $this->assertTrue(file_exists(Config::get('nasta.local_entries_path') . $file->path_local));
  }

  public function testFileHash(){
    $path = storage_path() . self::$testSourceFile;
    $hash = DropboxDownloadFile::computeFileHash($path);
    
    $this->assertEquals($hash, self::$testSourceHash);
  }

  public function testFileHashMultipart(){
    $targetHash = "b010ccdde44501ace5382336369e99e3f76e92235f7192bdf8bba2d191ca4494";

    $path = storage_path() . "/test/sample.mp4";
    $hash = DropboxDownloadFile::computeFileHash($path);
    
    $this->assertEquals($hash, $targetHash);
  }

}