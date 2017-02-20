<?php

namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DataIntegrityException;

use App\Database\Entry\Entry;
use App\Database\Category\Category;
use App\Database\Upload\UploadedFile;

use App;
use Auth;
use Response;

class StationController extends Controller
{	
	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function dashboard()
	{
		return view('station.dashboard');
	}

	public function categories()
	{
		$groupedCategories = Category::getAllGrouped(null, true);

		return view('station.categories', compact('groupedCategories'));
	}

	public function submission(Category $category)
	{
		if ($category->constraints->isEmpty())
			throw new DataIntegrityException("No file constraints for category: " . $category->id);

		// Check if there is an entry or one can be created
		$closed = !$category->canEditSubmissions();
		if ($closed && !$category->myEntry != null)
			return App::abort(404);

		// create or find an entry
		$entry = $category->myEntryOrNew();
		$readonly = $closed || $entry->submitted == 1;
		$filename = Auth::user()->compact_name . "_" . $category->compact_name . "_ExampleFile.mp4";

		return view('station.submission.index', compact('category', 'entry', 'readonly', 'closed', 'filename'));
	}

	public function files()
	{
		$categories = Category::with('myEntry')->get();
		$categories = array_filter($categories->all(), function($cat){ return $cat->myEntry == null || !$cat->myEntry->submitted; });

		$files = Auth::user()->uploadedFiles()->orderBy("category_id")
			->with('category')->with('category.myEntry')->with('metadata')
			->get();

		return view('station.files', compact('files', 'categories'));
	}

	public function submission_files(Category $category)
	{
		$entry = $category->myEntryOrNew();
		if ($entry == null)
			return Response::json([
				"expected_count" => $category->constraints()->count(),
				"files" => [],
			]);

		$files = $entry->uploadedFiles->map(function ($f){
			$errors = [];
			if ($f->rule_break != null){
				$errs = json_decode($f->rule_break->errors, true);
				foreach ($errs as $err){
					$errors[] = trans("rule_break.error." . $err);
				}
			}

			return [
				"id" => $f->id,
				"name" => $f->name,
				"uploaded_at" => $f->uploaded_at->toDayDateTimeString(),
				"type" => $f->metadata == null ? "other" : "video",
				"url" => route('station.files.download', $f),
				"errors" => json_encode($errors),
				"status" => $f->rule_break == null ? "pending" : (count($errors) == 0 ? "ok" : "fail"),
			];
		});

		return Response::json([
			"expected_count" => $category->constraints()->count(),
			"files" => $files,
		]);
	}


	public function results()
	{

	}
}
