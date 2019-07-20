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
Route::post('login', 'Api\LoginController@login');


Route::group(['middleware' => [ 'auth:api']], function () {
    Route::post( '/master', 'Api\TiketController@getMaster')->name( 'getMaster');
    Route::post('/createTiket', 'Api\TiketController@store')->name('createTiketApi');
    Route::post('/reply', 'Api\TiketController@replyTiket')->name('replyTiketTiketApi');

});
