<?php 
namespace App\Helpers;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use Exception;

class DropboxHelper {

  private $client;

  private __construct($access_token){
    // if(App::environment('testing', 'test'))
    //   return null; // TODO - properly
    //   // $account = "devtest";

    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $access_token);
    $this->client = new Dropbox($client);
  }

  public delete($path){
    try {
      $this->client->delete($path);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public ensureFileExists($src, $dest){
    try {
      $this->client->simpleUpload($src, $dest);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

}
