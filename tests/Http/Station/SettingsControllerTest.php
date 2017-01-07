<?php
namespace Tests\Http\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use TestCase;

use App\Database\User;

use Hash;

class SettingsControllerTest extends TestCase
{
  use DatabaseTransactions;

  private static $saveUrl = '/station/settings';

  private function assertUser($user, $expected){
    $this->assertNotNull($user);

    if (isset($expected['email']))
      $this->assertEquals($expected['email'], $user->email);
    if (isset($expected['password']))
      $this->assertTrue(Hash::check($expected['password'], $user->password));
  }

  private function assertSave($data){
    $this->actingAs($this->station)->postAjax(self::$saveUrl, $data)
      ->assertResponseOk();

    $user = User::find($this->station->id);
    $this->assertUser($user, $data);

    return $user;
  }

  private function assertSaveFail($data, $expected, $status){
    $this->actingAs($this->station)->postAjax(self::$saveUrl, $data)
      ->assertResponseStatus($status);

    $user = User::find($this->station->id);
    $this->assertUser($user, $expected);

    return $user;
  }


  public function testAuthorization()
  {
    $this->postAjax(self::$saveUrl)
        ->assertResponseStatus(401);

    $this->actingAs($this->station)->postAjax(self::$saveUrl)
        ->assertResponseStatus(422);

    $this->actingAs($this->judge)->postAjax(self::$saveUrl)
        ->assertResponseStatus(403);

    $this->actingAs($this->admin)->postAjax(self::$saveUrl)
        ->assertResponseStatus(403);
  }

  public function testSave(){
    $data = [
      'email' => "blah@email.com",
      'password' => "testing12345",
      'password_confirmation' => "testing12345"
    ];

    $this->assertSave($data);
  }
  public function testSaveNoPassword(){
    $data = [
      'email' => "blah@email.com"
    ];
    $origPassword = $this->station->password;

    $user = $this->assertSave($data);
    $this->assertEquals($origPassword, $user->password);
  }

  public function testSaveDuplicateEmail(){
    $origData = User::find($this->station->id)->toArray();
    $data = [
      'email' => "test@email.com",
      'password' => "testing12345",
      'password_confirmation' => "testing12345"
    ];

    $user = $this->assertSaveFail($data, $origData, 422);
    $this->assertFalse(Hash::check($data['password'], $user->password));
  }

  public function testSaveUnmatchedPassword(){
    $origData = User::find($this->station->id)->toArray();
    $data = [
      'email' => "blah@email.com",
      'password' => "testing12345",
      'password_confirmation' => "testing22222"
    ];

    $user = $this->assertSaveFail($data, $origData, 422);
    $this->assertFalse(Hash::check($data['password'], $user->password));
  }

  public function testSaveMissingPasswordConfirm(){
    $origData = User::find($this->station->id)->toArray();
    $data = [
      'email' => "blah@email.com",
      'password' => "testing12345",
    ];

    $user = $this->assertSaveFail($data, $origData, 422);
    $this->assertFalse(Hash::check($data['password'], $user->password));
  }

}