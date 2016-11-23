<?php

namespace App\Http\Controllers\Station;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Exceptions\DataIntegrityException;

use App\Database\Category\Category;

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
		$categories = Category::all();

		return view('station.categories', compact('categories'));
	}

	public function submission($slug)
	{
		$category = Category::findBySlug($slug);

		if ($category->constraints->isEmpty())
			throw new DataIntegrityException("No file constraints for category: ".$slug);

		return view('station.submission.index', compact('category'));
	}
}
