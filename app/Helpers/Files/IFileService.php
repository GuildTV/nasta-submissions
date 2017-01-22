<?php 
namespace App\Helpers\Files;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use Exception;
use Serializable;

interface IFileService extends Serializable {
  public function delete($path);

  public function ensureFileExists($src, $dest);

  public function fileExists($path);

  public function listFolder($path);

  public function move($src, $dest);

  public function download($src, $dest);

  public function getPublicUrl($path);

  public function getMetadata($path);
}
