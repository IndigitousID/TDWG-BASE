<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register',			'LoginController@register');
Route::post('login', 			'LoginController@login');
Route::post('forget-password', 	'LoginController@forgetpassword');
Route::post('reset-password', 	'LoginController@resetPassword');

// Route::prefix('saya')->middleware(['auth:api'])->group(function () {
Route::prefix('saya')->middleware(['bypass'])->group(function () {
	Route::get('', 					'MeController@me');
	Route::get('notifikasi',		'MeController@notifikasi');
	Route::post('notifikasi',		'MeController@baca_notifikasi');
	
	/*----------  SETTING  ----------*/
	Route::apiResource('preferensi','PreferenceController');
	
	Route::post('change-password', 	'MeController@change_password');
	Route::post('logout',			'LoginController@logout');
});

/*----------  CONFIG  ----------*/
Route::prefix('pengaturan')->group(function () {
	Route::get('beranda', 		'ConfigController@beranda');
	Route::get('direktori', 	'ConfigController@direktori');
	Route::get('subdirektori', 	'ConfigController@subdirektori');
	Route::get('resource', 		'ConfigController@resource');
	Route::get('resource/{id}',	'ConfigController@resource_show');
});

