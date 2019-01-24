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
    return Redirect::route('hierarchy');
});

Route::get('/hierarchy','HierarchyController@index')->name('hierarchy');
Route::get('/list', 'ListController@index')->name('list');
Route::get('/list/data/workers', 'ListController@dataWorkers')->name('dataWorkers');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
