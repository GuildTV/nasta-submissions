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
		$categories = Category::with('myEntry')->get();

		return view('station.categories', compact('categories'));
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

		return view('station.submission.index', compact('category', 'entry', 'readonly', 'closed'));
	}

	public function files()
	{
		$categories = Category::all();
		$files = UploadedFile::orderBy("category_id")->with('category')->get();

		return view('station.files', compact('files', 'categories'));
	}


	public function results()
	{

	}
}
