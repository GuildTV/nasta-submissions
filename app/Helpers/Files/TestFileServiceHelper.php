<?php 
namespace App\Helpers\Files;

use App\Helpers\Files\IFileService;

use Exception;

class TestFileServiceHelper implements IFileService{

  private $debug = false;

  private $files = [];
  private $operations = [];

  public function __construct($files, $debug){
    $this->files = $files;
    $this->debug = $debug;
  }

  public function serialize()
  {
    return serialize([
      $this->debug,
      $this->files,
      $this->operations,
    ]);
  }

  public function unserialize($data)
  {
    list(
      $this->debug,
      $this->files,
      $this->operations,
    ) = unserialize($data);
  }

  private function addOperation($op){
    $this->operations[] = $op;

    if ($this->debug)
      var_dump($op);
  }

  public function getOperations(){
    return $this->operations;
  }

  private function genFile($path, $file){
    $pathlen = strlen($path);
    if ($pathlen > 0) $pathlen++;

    return [
        "name"     => substr($file['name'], $pathlen),
        "modified" => $file['modified'],
        "size"     => $file['size'],
        "rev"      => $file['rev'],
        "hash"     => $file['hash'],
      ];
  }

  public function delete($path){
    $this->addOperation([ "delete", $path ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $path)
        continue;

      unset($this->files[$k]);
      return true;
    }

    return false;
  }

  public function ensureFileExists($src, $dest){
    throw new Exception("Not supported");
  }

  public function fileExists($path){
    $this->addOperation([ "exists", $path ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $path)
        continue;

      return true;
    }

    return false;
  }

  public function listFolder($path){
    $this->addOperation([ "list", $path ]);

    $res = null;

    foreach ($this->files as $k=>$f){
      if (strpos($f['name'], $path) === false)
        continue;

      $res[] = $this->genFile($path, $f);
    }

    return $res;
  }

  public function move($src, $dest){
    $this->addOperation([ "move", $src, $dest ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $src)
        continue;

      $this->files[$k]['name'] = $dest;

      // Not sure how best to determint the parent foldername
      return $this->genFile("", $this->files[$k]);
    }

    return false;
  }

  public function download($src, $dest){
    $this->addOperation([ "download", $src, $dest ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $src)
        continue;

      return true;
    }

    return false;
  }

  public function upload($src, $dest){
    throw new Exception("Not implemented!");
  }

  public function getPublicUrl($path){
    $this->addOperation([ "url", $path ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $path)
        continue;

      return "http://fakebox.com/" . str_random(10);
    }

    return null;
  }

  public function getMetadata($path){
    $this->addOperation([ "metadata", $path ]);

    foreach ($this->files as $k=>$f){
      if ($f['name'] != $path)
        continue;

      return $f['metadata'];
    }

    return null;
  }

}
