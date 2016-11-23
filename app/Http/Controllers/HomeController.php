<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Route;
use Redirect;

class HomeController extends Controller
{

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('home');
	}
	

	public function redirect()
	{
		$route = Route::getCurrentRoute();

		if($route) {
			$actions = $route->getAction();

			if(array_key_exists('target', $actions)) {
				return Redirect::to($actions['target']);
			}
		}

		return abort(404);
	}
}
