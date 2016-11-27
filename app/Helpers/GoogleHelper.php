<?php 
namespace App\Helpers;

use Google_Client;
use Google_Exception;
use Google_Service_Drive;
use Google_Auth_AssertionCredentials;

use Exception;
use Request;
use Config;
use App;
use File;

class GoogleHelper {
  public static function loadClient($id){
    if(App::environment('testing', 'test'))
      return null; // TODO - properly
      // $account = "devtest";

    try{
      $client = new Google_Client();
      $client->setApplicationName("NaSTA Submissions - ".Request::getHttpHost());
      $client->setScopes(['https://www.googleapis.com/auth/drive']);
      // $client->setAuthConfig(storage_path()."/google_accounts/".$id.".json");
      // $client->setSubject("big.big.julez@gmail.com");

      $json_path = storage_path()."/google_accounts/".$id.".json";
      //attempt oauth
      $client->setAuthConfig($json_path);
      $client->setRedirectUri(Request::getSchemeAndHttpHost().'/admin/google-auth/callback');
      $client->addScope('https://www.googleapis.com/auth/youtube');
      $client->setAccessType("offline");

      $token_path = storage_path()."/google_accounts/".$id.".access.json";

      if(!File::exists($token_path))
        return $client;

      //load access token
      $token = File::get($token_path);
      $client->setAccessToken($token);
        
      //check looks good
      if(!GoogleHelper::saveAccessToken($client, $id, $token))
        return null;

    } catch (Exception $e){
      dd($e);
      // TODO - log exception!
      return null;
    }

    return $client;
  }

  public static function saveAccessToken($client, $id, $oldToken=null){
    try{
      $token = $client->getAccessToken();
      if($token == null)
        return false;

      if(!isset($token['refresh_token']) && $oldToken != null){
        $token['refresh_token'] = $oldToken['refresh_token'];
      }

      //ensure file is up to date
      $token_path = storage_path()."/google_accounts/".$id.".access.json";
      File::put($token_path, json_encode($token));

      return true;
    } catch (Google_Exception $e){
      return false;
    }
  }


  public static function getDriveClient($id){
    $client = self::loadClient($id);
    if ($client == null)
      return null;

    return new Google_Service_Drive($client);
  }
}
