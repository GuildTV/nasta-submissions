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
        "name" => substr($file['name'], $pathlen),
        "modified" => $file['modified'],
        "size" => $file['size'],
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

}
