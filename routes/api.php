<?php
Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/user', function (){
    return Auth::user();
})->name('user');

Route::post('/twitter/logout', 'TwitterAuthController@logout')->name('twitter.logout');
Route::delete('/twitter/{id}', 'TwitterAuthController@delete')->name('twitter.delete');
Route::get('/twitter/id', 'TwitterAuthController@getId')->name('twitter.getId');
Route::post('/twitter/{id}', 'TwitterAuthController@setId')->name('twitter.setId');

Route::get('/twitter/user/list', 'TwitterUserController@list')->name('twitter.list');
Route::get('/twitter/user/info/{id}', 'TwitterUserController@info')->name('twitter.info');

Route::post('/filter', 'FilterWordController@add')->name('filter.add');
Route::get('/filter', 'FilterWordController@show')->name('filter.show');
Route::get('/filter/{id}', 'FilterWordController@showOneFilter')->name('filter.showOne');
Route::put('/filter/{id}', 'FilterWordController@editFilter')->name('edit.filter');
Route::delete('/filter/{id}', 'FilterWordController@deleteFilter')->name('filter.delete');