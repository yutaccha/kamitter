<?php

Route::group(['prefix' => 'kamitter/public'], function (){


    /**
     * sessionトークンのリフレッシュ
     * CRSFトークンのリフレッシュ
     */
    Route::get('/token/refresh', function (\Illuminate\Http\Request $request) {
        $request->session()->regenerateToken();
        return response()->json();
    });

    /**
     * ユーザー管理API
     */
    Route::post('/register', 'Auth\RegisterController@register')->name('register');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/user', function () {
        return Auth::user();
    })->name('user');


    /**
     * パスワードリマインダーAPI
     */
    Route::group([
        'namespace' => 'Auth',
        'middleware' => 'api',
        'prefix' => 'password'
    ], function () {
        Route::post('/create', 'PasswordResetController@create');
        Route::get('/find/{token}', 'PasswordResetController@find');
        Route::post('/reset', 'PasswordResetController@reset');
    });

    /**
     * ツイッターアカウント管理API
     */
    Route::post('/twitter/logout', 'TwitterAuthController@logout')->name('twitter.logout');
    Route::delete('/twitter/{id}', 'TwitterAuthController@delete')->name('twitter.delete');
    Route::get('/twitter/id', 'TwitterAuthController@getId')->name('twitter.getId');
    Route::post('/twitter/{id}', 'TwitterAuthController@setId')->name('twitter.setId');


    /**
     * ツイッターアカウントの情報取得API
     */
    Route::get('/twitter/user/list', 'TwitterUserController@list')->name('twitter.list');
    Route::get('/twitter/user/info/{id}', 'TwitterUserController@info')->name('twitter.info');

    /**
     * フォローターゲット管理API
     */
    Route::post('/follow', 'FollowTargetController@add')->name('follow.add');
    Route::get('/follow', 'FollowTargetController@show')->name('follow.show');
    Route::put('/follow/{id}', 'FollowTargetController@edit')->name('follow.edit');
    Route::delete('/follow/{id}', 'FollowTargetController@delete')->name('follow.delete');


    /**
     * 自動いいねの管理API
     */
    Route::post('/like', 'AutomaticLikeController@add')->name('like.add');
    Route::get('/like', 'AutomaticLikeController@show')->name('like.show');
    Route::put('/like/{id}', 'AutomaticLikeController@edit')->name('like.edit');
    Route::delete('/like/{id}', 'AutomaticLikeController@delete')->name('like.delete');

    /**
     * 自動ツイートの管理API
     */
    Route::post('/tweet', 'AutomaticTweetController@add')->name('tweet.add');
    Route::get('/tweet', 'AutomaticTweetController@show')->name('tweet.show');
    Route::put('/tweet/{id}', 'AutomaticTweetController@edit')->name('tweet.edit');
    Route::delete('/tweet/{id}', 'AutomaticTweetController@delete')->name('tweet.delete');

    /**
     * 条件キーワードの管理API
     */
    Route::post('/filter', 'FilterWordController@add')->name('filter.add');
    Route::get('/filter', 'FilterWordController@show')->name('filter.show');
    Route::get('/filter/{id}', 'FilterWordController@showOneFilter')->name('filter.showOne');
    Route::put('/filter/{id}', 'FilterWordController@editFilter')->name('edit.filter');
    Route::delete('/filter/{id}', 'FilterWordController@deleteFilter')->name('filter.delete');

    /**
     * システムON/OFF操作API
     */
    Route::get('/system/status', 'SystemManagerController@show')->name('system.show');
    Route::post('/system/run', 'SystemManagerController@run')->name('system.run');
    Route::post('/system/stop', 'SystemManagerController@stop')->name('system.stop');

});