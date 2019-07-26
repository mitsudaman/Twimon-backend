<?php

use Illuminate\Http\Request;

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
// Route::get('/og/{id}', 'OGPController@index')->name('ogp.index');
// Route::get('/', function () { return 'Hello World'; });
Route::get('/login/twitter', 'Auth\LoginController@redirectToProvider');
Route::get('/login/twitter/callback', 'Auth\LoginController@handleProviderCallback');