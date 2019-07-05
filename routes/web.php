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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => [ 'web', 'auth']], function () {
    Route::get('/admin', 'HomeController@index')->name('home');
    Route::get('/laravel-filemanager', '\UniSharp\LaravelFilemanager\Controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\UniSharp\LaravelFilemanager\Controllers\UploadController@upload');
    Route::prefix('/admin/master')->group(function () {
        Route::resource('roles', 'RoleController');
        Route::resource('users', 'UserController');
        Route::resource('departement', 'DepartementController');
        Route::resource('permission', 'PermissionController');
        Route::resource('prioritas', 'PrioritasController');
        Route::resource('status', 'StatusController');

    });
    Route::prefix('/admin/page')->group(function () {
        Route::resource('tiket', 'TiketController');
        Route::post('tiket/$id', 'TiketController@replyTiket')->name( 'tiket.replyTiket');

    });
    Route::prefix('/admin/ajax/master/')->group(function () {
        Route::get('users', 'UserController@getDataMaster')->name('ajax.master.users');
        Route::post('users', 'UserController@create')->name('ajax.master.users');
        Route::put('users', 'UserController@update')->name('ajax.master.users');
        Route::delete('users', 'UserController@update')->name('ajax.master.users');
        Route::get('roles', 'RoleController@show')->name('ajax.master.roles');
    });
});
