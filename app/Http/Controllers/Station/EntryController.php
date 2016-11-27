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

use App\Helpers\GoogleHelper;

use DB;
use Auth;
use App;
use Config;
use Redirect;

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

    return $entry;
  }

  public function init_upload(Category $category, FileConstraint $constraint){
    // dd($category->constraints);
    if (!$category->hasPivot('constraints', $constraint))
      throw new DataIntegrityException("No such constraint for category");

    // TODO - check for existing session

    $account = GoogleAccount::ChooseForNewUpload();
    if ($account == null)
      throw new UploadException("Unable to choose target");

    $client = GoogleHelper::getDriveClient($account->id);
    if ($client == null)
      throw new UploadException("Unable to initialize api");

    $user = Auth::user();

    $fileMetadata = new Google_Service_Drive_DriveFile([ // TODO - set write permissions
      'name' => $user->name . " " . $category->name . " - " . $constraint->name,
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

    FileUpload::create([
      'station_id' => $user->id,
      'category_id' => $category->id,
      'constraint_id' => $constraint->id,
      'account_id' => $account->id,
      'scratch_folder_id' => $folderId
    ]);

    $url = "https://drive.google.com/drive/folders/" . $folderId;
    return Redirect::to($url);
  }

}
