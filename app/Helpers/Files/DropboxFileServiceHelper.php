<?php 
namespace App\Helpers\Files;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Helpers\Files\IFileService;

use App\Mail\Admin\ExceptionEmail;

use Exception;
use Storage;

use Carbon\Carbon;

class DropboxFileServiceHelper implements IFileService{

  private $client;

  public function __construct($access_token){
    // if(App::environment('testing', 'test'))
    //   return null; // TODO - properly
    //   // $account = "devtest";
    $this->access_token = $access_token;
    $this->init();
  }

  private function init(){
    $client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'), $this->access_token);
    $this->client = new Dropbox($client);
  }

  public function serialize()
  {
    return serialize([
      $this->access_token,
    ]);
  }

  public function unserialize($data)
  {
    list(
      $this->access_token,
    ) = unserialize($data);

    $this->init();
  }

  private function genFile($file){
    // var_dump($file->getData());
    return  [
      "name"     => $file->getName(),
      "modified" => Carbon::parse($file->getServerModified()),
      "size"     => $file->getSize(),
      "rev"      => $file->getRev(),
      "hash"     => $file->__get("content_hash"),
    ];
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
        $res[] = $this->genFile($file);
      }

      return $res;
    } catch (Exception $e) {
      return null;
    }
  }

  public function move($src, $dest){
    try {
      $file = $this->client->move($src, $dest);
      return $this->genFile($file);

    } catch (Exception $e) {
      return null;
    }
  }

  public function download($src, $dest){
    $fp = null;
    $ch = null;

    try {
      // curl -X POST https://content.dropboxapi.com/2/files/download \
      // --header "Authorization: Bearer <get access token>" \
      // --header "Dropbox-API-Arg: {\"path\": \"/Homework/math/Prime_Numbers.txt\"}"

      //This is the file where we save the information
      $fp = fopen($dest, 'w+');

      //Here is the file we are downloading, replace spaces with %20
      $ch = curl_init("https://content.dropboxapi.com/2/files/download");
      curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 3gb oveer 5min = 160mbit/s
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $this->access_token,
        "Dropbox-API-Arg: ". json_encode([ "path" => $src ]),
      ]);

      // write curl response to file
      curl_setopt($ch, CURLOPT_FILE, $fp); 
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

      // get curl response
      curl_exec($ch); 
      $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if ($status != 200)
        throw new Exception("Bad status code downloading file");

      // close up
      curl_close($ch);
      fclose($fp);

      return true;
    } catch (Exception $e) {
      if ($fp != null)
        fclose($fp);
      if ($ch != null)
        curl_close($ch);
      throw $e;
    }
  }

  public function upload($src, $dest){
    try {
      $file = $this->client->upload($src, $dest);
      return $this->genFile($file);

    } catch (Exception $e) {
      return null;
    }
  }

  public function getPublicUrl($path){
    $data = null;
    try {
      $response = $this->client->postToAPI("/sharing/create_shared_link_with_settings", ["path" => $path, "settings" => ['requested_visibility' => 'public']]);
      $data = $response->getDecodedBody();
      
    } catch (Exception $e) {
      try {
        $response = $this->client->postToAPI("/sharing/list_shared_links", ["path" => $path, 'direct_only' => true]);
        if ($response == null)
          return null;

        $data = $response->getDecodedBody();
        $data = $data['links'][0];

      } catch (Exception $e2) {
        return null;
      }
    }

    if (!isset($data['url']))
      return null;

    if (!isset($data['link_permissions']))
      return null;

    $perms = $data['link_permissions'];
    if (!isset($perms['resolved_visibility']) || !isset($perms['resolved_visibility']['.tag']))
      return null;

    if ($perms['resolved_visibility']['.tag'] != "public")
      return null;

    return $data['url'];
  }

  public function getMetadata($path){
    try {
      $res = $this->client->getMetadata($path, [ "include_media_info" => true ]);
      $data =  $res->getMediaInfo();
      if ($data == null)
        return null;

      return $data->getData();
    } catch (Exception $e) {
      return null;
    }
  }

}