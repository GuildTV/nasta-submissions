<?php
namespace Tests\Mail\Admin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Mail\Admin\ExceptionEmail;

use App\Exceptions\InvalidArgumentException;

use Exception;

class ExceptionEmailTest extends TestCase
{
  public function testEmail()
  {
    try {
      throw new InvalidArgumentException("test exception");
    } catch (Exception $e){
      // Note: Not rendering the email, but no error was thrown whilst trying to do so
      ExceptionEmail::notifyAdmin($e);
      $this->assertEmailCount(1);
    }
  }

}