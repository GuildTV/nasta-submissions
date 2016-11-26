<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ 'uses' => 'HomeController@redirect', 'target' => '/login' ]);

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name("auth.reset");
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Route::get('/dashboard', 'HomeController@dashboard')->middleware('auth:web');


$router->group([
  'middleware' => ['auth:web', 'can:station'],
  'prefix' => 'station'
], function ($router) {

  Route::get('/dashboard', 'Station\StationController@dashboard')->name("station.dashboard");
  Route::get('/categories', 'Station\StationController@categories')->name("station.categories");
  Route::get('/results', 'Station\StationController@results')->name("station.results");
  Route::get('/categories/{category}', 'Station\StationController@submission')->name("station.submission");

  Route::post('/categories/{category}/submit', 'Station\EntryController@submit')->name("station.entry.submit");
});