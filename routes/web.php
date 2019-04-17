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

Route::get('auth/twitter/provider', 'TwitterAuthController@redirectToProvider')->name('twitter.provide');
Route::get('auth/twitter/callback', 'TwitterAuthController@handleProviderCallback')->name('twitter.callback');
Route::get('auth/twitter/logout', 'TwitterAuthController@logout')->name('twitter.logout');

// 他のルートに該当しない場合indexを返す
Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.+');