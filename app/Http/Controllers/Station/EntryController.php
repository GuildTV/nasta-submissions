<?php

namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DataIntegrityException;
use App\Exceptions\UploadException;

use App\Http\Requests\Station\SubmitRequest;

use App\Database\Category\Category;
use App\Database\Category\FileConstraint;
use App\Database\Upload\GoogleAccount;
use App\Database\Entry\FileUpload;
use App\Database\Entry\EntryFolder;

use App\Helpers\GoogleHelper;

use DB;
use Auth;
use App;
use Config;
use Redirect;
use Exception;

use Google_Service_Drive_DriveFile;

class EntryController extends Controller
{ 
  public function submit(SubmitRequest $request, Category $category)
  {
    DB::beginTransaction();

    $category->entries()->where('station_id', Auth::user()->id)->delete();

    $entry = $category->getEntryForStation(Auth::user()->id); // Gets an empty entry

    $entry->name = $request->input('name');
    $entry->description = $request->input('description');
    $entry->rules = $request->has('rules') && $request->input('rules');
    $entry->submitted = $request->has('submit') && $request->input('submit');
    $entry->save();

    DB::commit();

    // Change the google drive folder to be readonly
    if ($entry->submitted){
      $folder = $entry->folder();
      if ($folder != null){
        // TODO - change permissions on upload folder to private

      }
    }

    return $entry;
  }

  public function embedFolder(Category $category){
    $folder = EntryFolder::findForStation(Auth::user()->id, $category->id);
    if ($folder != null){
      return Redirect::to("https://drive.google.com/embeddedfolderview?id=" . $folder->folder_id . "#list");
    }

    return view('station.submission.embed');
  }

  public function init_upload(Category $category){
    $user = Auth::user();

    $folder = EntryFolder::findForStation($user->id, $category->id);
    if ($folder != null){
      return Redirect::to($this->folderUrl($folder->folder_id));
    }

    $account = GoogleAccount::ChooseForNewUpload();
    if ($account == null)
      throw new UploadException("Unable to choose target");

    $client = GoogleHelper::getDriveClient($account->id);
    if ($client == null)
      throw new UploadException("Unable to initialize api");


    $fileMetadata = new Google_Service_Drive_DriveFile([ // TODO - set write permissions
      'name' => $user->name . " " . $category->name,
      'mimeType' => 'application/vnd.google-apps.folder',
    ]);
    $file = $client->files->create($fileMetadata, ['fields' => 'id']);
    if ($file == null)
      throw new UploadException("Failed to create upload folder");

    $folderId = $file->id;

    $copyMetadata = new Google_Service_Drive_DriveFile([
      "name" => "How to use this folder etc", // TODO
      "parents" => [ $folderId ],
      'appProperties' => [
        'ignore' => 'rules' // mark as ignore due to being the rules pdf
      ],
    ]);
    $client->files->copy(Config::get('nasta.drive_rules_file'), $copyMetadata);

    try {
      EntryFolder::create([
        'station_id' => $user->id,
        'category_id' => $category->id,
        'folder_id' => $folderId,
      ]);
    } catch (Exception $e) {
      // We shall assume one exists and we hit the unique constraint
      $folder = EntryFolder::findForStation($user->id, $category->id);

      // delete the fresh folder
      $client->files->delete($folderId);

      if ($folder == null)
        throw new UploadException("Unable to record upload folder");

      return Redirect::to($this->folderUrl($folder->folder_id));;
    }

    return Redirect::to($this->folderUrl($folderId));
  }

  private function folderUrl($id){
    return "https://drive.google.com/drive/folders/" . $id;
  }

}
