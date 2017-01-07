<?php
namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\DropboxHelper;

use App\Exceptions\UploadException;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox;

use App\Database\Category\Category;
use App\Database\Upload\UploadedFile;
use App\Database\Upload\UploadedFileLog;

use App;
use Auth;
use Redirect;
use Exception;

class FileController extends Controller
{ 
  public function delete(UploadedFile $file)
  {
    // Check file belongs to user
    if ($file->station_id != Auth::user()->id)
      return App::abort(404);

    // Check if category can be edited
    if ($file->category != null && !$file->category->canEditSubmissions())
      return App::abort(400);

    if ($file->account == null)
      throw new UploadException("Missing account on file");

    // delete file on dropbox
    $client = new DropboxHelper($file->account->access_token);
    if ($client->delete($file->path)) {
      // track deletion
      UploadedFileLog::create([
        'station_id' => $file->station_id,
        'category_id' => $file->category_id,
        'level' => 'info',
        'message' => 'Delete \'' . $file->name . '\'',
      ]);
    } else {
      UploadedFileLog::create([
        'station_id' => $file->station_id,
        'category_id' => $file->category_id,
        'level' => 'warning',
        'message' => 'File \'' . $file->name . '\' has been deleted, but may remain on Dropbox',
      ]);
    }

    // delete from our db
    $file->delete();

    return $file;
  }

  public function link(UploadedFile $file, Category $category)
  {
    // Check file belongs to user
    if ($file->station_id != Auth::user()->id)
      return App::abort(404);

    if ($file->category_id != null)
      return App::abort(400);

    if (!$category->canEditSubmissions())
      return App::abort(400);

    // update file
    $file->category_id = $category->id;
    $file->save();

    UploadedFileLog::create([
      'station_id' => $file->station_id,
      'category_id' => $file->category_id,
      'level' => 'info',
      'message' => 'Manually linked file \'' . $file->name . '\' to category \'' . $category->name . '\'',
    ]);

    return $file;
  }

}
