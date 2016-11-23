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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');


$router->group([
  'middleware' => ['auth:web', 'can:station'],
  'prefix' => 'station'
], function ($router) {

  Route::get('/dashboard', 'Station\StationController@dashboard')->name("station.dashboard");
  Route::get('/categories', 'Station\StationController@categories')->name("station.categories");
  Route::get('/results', 'Station\StationController@results')->name("station.results");
  Route::get('/categories/{category}', 'Station\StationController@submission')->name("station.submission");

});