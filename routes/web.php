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

Route::get('/og/{id}', 'OGPController@index')->name('ogp.index');
Route::get('login/twitter', 'Auth\LoginController@redirectToProvider');
Route::get('og/twitter/callback', 'OGPController@handleProviderCallback');

