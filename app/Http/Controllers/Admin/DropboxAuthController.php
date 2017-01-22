<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\AjaxRequest;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Database\Upload\DropboxAccount;

use App\Helpers\StringHelper;

use Redirect;
use Session;
use Exception;

class DropboxAuthController extends Controller {

  public function index(){
    $accounts = [];

    $entries = DropboxAccount::get();
    foreach ($entries as $entry) {
      $valid = $this->test_account($entry);

      $used = $entry->total_space > 0 ? ($entry->used_space / $entry->total_space ) * 100 : 0;

      $accounts[] = [
        "name" => $entry->id,
        "valid" => $valid ? "Yes" : "No",
        "enabled" => $entry->enabled ? "Yes" : "No",
        "usedSpace" => StringHelper::formatBytes($entry->used_space, 2),
        "totalSpace" => StringHelper::formatBytes($entry->total_space, 2),
        "percentageUsed" => round($used, 2) . "%",
      ];
    }

    return view('admin.dropboxauth', compact('accounts'));
  }

  private function test_account($entry){
    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $entry->access_token);
    $dropbox = new Dropbox($client);

    try {
      $accountSpace = $dropbox->getSpaceUsage();

      $entry->used_space = $accountSpace['used'];
      $entry->total_space = $accountSpace['allocation']['allocated'];
      $entry->save();
    } catch (Exception $e){
      return false;
    }

    return true;
  }

  public function go(AjaxRequest $r){
    if(!$r->has('account'))
      return Redirect::to('/admin/dropbox-auth')
        ->withErrors([
          'err'=>["You must select an account to revalidate"]
        ]);

    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'));
    $dropbox = new Dropbox($client);

    $authHelper = $dropbox->getAuthHelper();
    $authUrl = $authHelper->getAuthUrl(env('APP_URL') . "/admin/dropbox-auth/callback");
    Session::flash('admin.dropboxauth.account', $r->account);

    return Redirect::to($authUrl);
  }

  public function callback(AjaxRequest $request){
    if(!Session::has('admin.dropboxauth.account'))
      return Redirect::to('/admin/dropbox-auth')
        ->withErrors([
          'err'=>["Encounted an unexpected error"]
        ]);

    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'));
    $dropbox = new Dropbox($client);
    $authHelper = $dropbox->getAuthHelper();

    if (!$request->has('code') || !$request->has('state'))
      return Redirect::to('/admin/dropbox-auth')
        ->withErrors([
          'err'=>["Encounted an unexpected error"]
        ]);

    $accessToken = $authHelper->getAccessToken($request->code, $request->state, env('APP_URL') . "/admin/dropbox-auth/callback");

    DropboxAccount::updateOrCreate([
      'id' => Session::get('admin.dropboxauth.account'),
      'enabled' => true,
      'access_token' => $accessToken->getToken(),
    ]);

    return Redirect::to('/admin/dropbox-auth')
      ->withErrors([
        'err'=>["Success"]
      ]);
  }

}
