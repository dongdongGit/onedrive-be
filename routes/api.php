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

Route::get('test', 'Admin\\UtilController@checkHealth');

Route::group((['prefix' => 'admin', 'namespace' => 'Admin']), function () {
    Route::post('login', 'AuthController@login')->name('admin.login.post');

    Route::group(['middleware' => ['auth:admin']], function () {
        Route::apiResource('setting', 'SettingController', ['as' => 'admin'])->only(['index', 'store']);
        Route::group(['prefix' => 'session'], function () {
            Route::post('/', 'SessionController@profile')->name('admin.session.profile');
        });
    });
});
