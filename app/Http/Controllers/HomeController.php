<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

use Route;
use Redirect;
use Auth;
use Exception;

class HomeController extends Controller
{

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function dashboard()
	{
		$user = Auth::user();
		if (!$user)
			throw new AuthenticationException();
		if ($user->can('station'))
			return Redirect::route('station.dashboard');
		if ($user->can('judge'))
			return Redirect::route('judge.dashboard');
		if ($user->can('admin'))
			return Redirect::route('admin.dashboard');

		throw new Exception("Unhandled user type in dashboard!");
	}

	public function rules()
	{
		return view('rules');
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
