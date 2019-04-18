<?php
Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/user', function (){
    return Auth::user();
})->name('user');

Route::get('/twitter/id', 'TwitterAuthController@getId')->name('twitter.id');
Route::get('/twitter/logout', 'TwitterAuthController@logout')->name('twitter.logout');

Route::get('/twitter/user/list', 'TwitterUserController@list')->name('twitter.list');