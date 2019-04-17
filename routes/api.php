<?php

Route::get('auth/twitter/provider', 'TwitterAuthController@redirectToProvider')->name('twitter.provide');
Route::get('auth/twitter/callback', 'TwitterAuthController@handleProviderCallback')->name('twitter.callback');
Route::get('auth/twitter/logout', 'TwitterAuthController@logout')->name('twitter.logout');

Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');