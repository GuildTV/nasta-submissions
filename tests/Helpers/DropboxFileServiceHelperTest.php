<?php
namespace Tests\Helpers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\Upload\DropboxAccount;

use App\Helpers\Files\DropboxFileServiceHelper;

use Carbon\Carbon;

class DropboxFileServiceHelperTest extends TestCase
{
  use DatabaseTransactions;

  private static $testAccountId = "test";
  private static $testSourceFile = "/test/test_file.png";

  public function testPublicUrl(){
    $filename = "/" . str_random(15) . ".tmp";

    // ensure file exists on dropbox
    $account = DropboxAccount::where("id", self::$testAccountId)->first();
    $dropbox = new DropboxFileServiceHelper($account->access_token);
    $this->assertTrue($dropbox->ensureFileExists(storage_path().self::$testSourceFile, $filename));

    $url = $dropbox->getPublicUrl($filename);
    $this->assertNotNull($url);
  }

}