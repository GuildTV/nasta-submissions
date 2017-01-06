<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\AjaxRequest;

use App\Helpers\GoogleHelper;
use App\Database\Upload\GoogleAccount;

use Redirect;
use File;
use Session;
use Exception;

class GoogleAuthController extends Controller {

  public function index(){
    $accounts = [];
    $usedNames = [];

    $files = scandir(storage_path()."/google_accounts/");
    foreach($files as $f){
      if($f == "." || $f == "..")
        continue;

      if(substr($f, -5) != ".json")
        continue;

      if(substr($f, -12) == ".access.json")
        continue;

      $name = substr($f, 0, -5);

      $client = GoogleHelper::loadClient($name);
      $valid = $client != null && (!$client->isAccessTokenExpired() || ($client->getAccessToken() != null));

      $data = [
        "name" => $name,
        "valid" => $valid ? "Valid" : "Expired",
      ];

      $dbEntry = GoogleAccount::where('id', $name)->first();
      if ($dbEntry == null){
        $data['enabled'] = "Unknown";
        $data['usedSpace'] = "-";
        $data['totalSpace'] = "-";
        $data['usedSpaceEstimate'] = "-";
        $data['percentageUsed'] = "-";
        $data['percentageUsedEstimate'] = "-";
      } else {
        $used = $dbEntry->total_space > 0 ? ($dbEntry->used_space / $dbEntry->total_space ) * 100 : 0;

        $data['enabled'] = $dbEntry->enabled ? "Yes" : "No";
        $data['usedSpace'] = $this->formatBytes($dbEntry->used_space, 2);
        $data['totalSpace'] = $this->formatBytes($dbEntry->total_space, 2);
        $data['usedSpaceEstimate'] = "0%"; // TODO
        $data['percentageUsed'] = round($used, 2) . "%";
        $data['percentageUsedEstimate'] = "0"; // TODO
      }

      $usedNames[] = $name;
      $accounts[] = $data;
    }

    $dbs = GoogleAccount::whereNotIn('id', $usedNames)->get();
    foreach ($dbs as $acc){
      $accounts[] = [
        "name" => $acc->name,
        "valid" => "Missing credentials",
        "enabled" => "Error",
        "usedSpace" => "-",
        "totalSpace" => "-",
        "usedSpaceEstimate" => "-",
        "percentageUsed" => "-",
        "percentageUsedEstimate" => "-"
      ];
    }

    // dd($accounts);
    return view('admin.googleauth', compact('accounts'));
  }

  public function go(AjaxRequest $r){
    if(!$r->has('account'))
      return Redirect::to('/admin/google-auth')
        ->withErrors([
          'err'=>["You must select an account to revalidate"]
        ]);

    $client = GoogleHelper::loadClient($r->account);

    if($client == null)
      return Redirect::to('/admin/google-auth')
        ->withErrors([
          'err'=>["Failed to load client"]
        ]);

    $client->setApprovalPrompt('force');

    $url = $client->createAuthUrl();
    Session::flash('admin.googleauth.account', $r->account);

    return Redirect::to($url);
  }

  public function callback(AjaxRequest $request){
    if(!Session::has('admin.googleauth.account'))
      return Redirect::to('/admin/google-auth')
        ->withErrors([
          'err'=>["Encounted an unexpected error"]
        ]);

    $client = GoogleHelper::loadClient(Session::get('admin.googleauth.account'));

    if($client == null)
      return Redirect::to('/admin/google-auth');

    try{
      $client->authenticate($request->code);
      $token = $client->getAccessToken();
    } catch (Exception $e) {
      return Redirect::to('/admin/google-auth')
        ->withErrors([
          'err'=>[$e->getMessage()]
        ]);
    }

    if(!GoogleHelper::saveAccessToken($client, Session::get('admin.googleauth.account')))
       return Redirect::to('/admin/google-auth')
        ->withErrors([
          'err'=>["Failed to save access token"]
        ]);

    GoogleAccount::create([
      "id" => Session::get('admin.googleauth.account'),
    ]);

    return Redirect::to('/admin/google-auth')
      ->withErrors([
        'err'=>["Success"]
      ]);
  }

  private function formatBytes($size, $precision = 2)
  {
    $base = log($size, 1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
  }

}
