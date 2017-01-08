<?php 
namespace App\Helpers\Files;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Helpers\Files\IFileService;

use Exception;

class DropboxFileServiceHelper implements IFileService{

  private $client;

  public function __construct($access_token){
    // if(App::environment('testing', 'test'))
    //   return null; // TODO - properly
    //   // $account = "devtest";

    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $access_token);
    $this->client = new Dropbox($client);
  }

  public function delete($path){
    try {
      $this->client->delete($path);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function ensureFileExists($src, $dest){
    try {
      $this->client->simpleUpload($src, $dest);
    } catch (Exception $e) {
    }

    return $this->fileExists($dest);
  }

  public function fileExists($path){
    try {
      return $this->client->getMetadata($path) != null;

    } catch (Exception $e) {
      return false;
    }
  }

  public function listFolder($path){
    try {
      $listFolderContents = $this->client->listFolder($path);

      $res = [];
      foreach ($listFolderContents->getItems() as $file){
        $res[] = [
          "name" => $file->getName(),
          "modified" => Carbon::parse($file->getServerModified()),
          "size" => $file->getSize(),
        ];
      }

      return $res;
    } catch (Exception $e) {
      return null;
    }
  }

  public function move($src, $dest){
    try {
      return $this->client->move($src, $dest);

    } catch (Exception $e) {
      return null;
    }
  }

}
