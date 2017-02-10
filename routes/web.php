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
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name("auth.forgot");
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name("auth.reset");
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Route::get('/dashboard', 'HomeController@dashboard')->middleware('auth:web');

Route::get('/help', 'HelpController@help')->name("help");
Route::get('/help/rules', 'HelpController@rules')->name("help.rules");
Route::get('/help/video-format', 'HelpController@video_format')->name("help.video-format");
Route::get('/help/contact', 'HelpController@contact')->name("help.contact");


$router->group([
  'middleware' => ['auth:web', 'can:station'],
  'prefix' => 'station'
], function ($router) {

  Route::get('/dashboard', 'Station\StationController@dashboard')->name("station.dashboard");
  Route::get('/categories', 'Station\StationController@categories')->name("station.categories");
  Route::get('/results', 'Station\StationController@results')->name("station.results");
  Route::get('/categories/{category}', 'Station\StationController@submission')->name("station.entry");
  Route::get('/categories/{category}/files', 'Station\StationController@submission_files')->name("station.entry.files");
  Route::get('/categories/{category}/upload', 'Station\EntryController@init_upload')->name("station.entry.upload");

  Route::get('/files', 'Station\StationController@files')->name("station.files");
  Route::post('/files/{file}/delete', 'Station\FileController@delete')->name("station.files.delete");
  Route::post('/files/{file}/link/{category}', 'Station\FileController@link')->name("station.files.link");
  Route::get('/files/{file}/download', 'Station\FileController@download')->name("station.files.download");

  Route::post('/categories/{category}/submit', 'Station\EntryController@submit')->name("station.entry.submit");
  Route::post('/categories/{category}/edit', 'Station\EntryController@edit')->name("station.entry.edit");

  Route::get('/settings', 'Station\SettingsController@settings')->name("station.settings");
  Route::post('/settings', 'Station\SettingsController@save')->name("station.settings.save");
});

$router->group([
  'middleware' => ['auth:web', 'can:judge'],
  'prefix' => 'judge'
], function ($router) {

  Route::get('/dashboard', 'Judge\JudgeController@dashboard')->name("judge.dashboard");
  Route::get('/entry/{entry}', 'Judge\JudgeController@view')->name("judge.view");
  Route::get('/download/{file}', 'Judge\JudgeController@download')->name("judge.download");
  Route::post('/entry/{entry}/score', 'Judge\JudgeController@score')->name("judge.score");
  Route::post('/finalize/{category}', 'Judge\JudgeController@finalize')->name("judge.finalize");

});

$router->group([
  'middleware' => ['auth:web', 'can:admin'],
  'prefix' => 'admin'
], function ($router) {
  Route::get('/dashboard', 'Admin\AdminController@dashboard')->name("admin.dashboard");

  Route::get('/submissions', 'Admin\SubmissionsController@dashboard')->name("admin.submissions");
  Route::get('/submissions/category/{category}', 'Admin\SubmissionsController@category')->name("admin.submissions.category");
  Route::get('/submissions/station/{station}', 'Admin\SubmissionsController@station')->name("admin.submissions.station");
  Route::get('/submissions/view/{station}/{category}', 'Admin\SubmissionsController@view')->name("admin.submissions.view");
  Route::get('/submissions/files', 'Admin\SubmissionsController@files')->name("admin.submissions.files");
  Route::get('/submissions/file/{file}', 'Admin\SubmissionsController@file')->name("admin.submissions.file");
  Route::post('/submissions/file/{file}/link/{category}', 'Admin\SubmissionsController@linkfile')->name("admin.submissions.file.link");
  Route::get('/submissions/file/{file}/download', 'Admin\SubmissionsController@download')->name("admin.submissions.file.download");
  Route::get('/submissions/file/{file}/metadata', 'Admin\SubmissionsController@metadata')->name("admin.submissions.file.metadata");

  Route::get('/rule-break/{entry}', 'Admin\RuleBreakController@index')->name("admin.rule-break");
  Route::get('/rule-break/{entry}/run', 'Admin\RuleBreakController@entry_recheck')->name("admin.rule-break.entry-check");
  Route::get('/rule-break/{entry}/{state}', 'Admin\RuleBreakController@entry_accept_reject')->name("admin.rule-break.entry-state");
  Route::get('/rule-break/{entry}/run/{file}', 'Admin\RuleBreakController@file_recheck')->name("admin.rule-break.file-check");
  Route::get('/rule-break/{entry}/{state}/{file}', 'Admin\RuleBreakController@file_accept_reject')->name("admin.rule-break.file-state");

  Route::get('/users', 'Admin\UsersController@dashboard')->name("admin.users");
  Route::get('/users/{user}', 'Admin\UsersController@view')->name("admin.users.view");
  Route::post('/users/{user}/save', 'Admin\UsersController@save');
  Route::post('/users/{user}/save/dropbox', 'Admin\UsersController@saveDropbox');

  Route::get('/google-auth', 'Admin\GoogleAuthController@index')->name("admin.googleauth");
  Route::get('/google-auth/go', 'Admin\GoogleAuthController@go')->name("admin.googleauth.go");
  Route::get('/google-auth/callback', 'Admin\GoogleAuthController@callback')->name("admin.googleauth.callback");

  Route::get('/dropbox-auth', 'Admin\DropboxAuthController@index')->name("admin.dropboxauth");
  Route::get('/dropbox-auth/go', 'Admin\DropboxAuthController@go')->name("admin.dropboxauth.go");
  Route::get('/dropbox-auth/callback', 'Admin\DropboxAuthController@callback')->name("admin.dropboxauth.callback");
});