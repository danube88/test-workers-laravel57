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
Route::get('/data/hierarchy','HierarchyController@dataHierarchy')->name('dataHierarchy');

Auth::routes();

Route::group(['prefix'=>'home'],function(){
  Route::get('/', 'HomeController@index')->name('home');
  Route::get('/data/workers', 'HomeController@dataWorkers')->name('homeWorkers');
  Route::resource('/crud', 'CRUDController',
                  [ 'only' => ['index','store','edit','update','destroy'],
                    'except'=>['show','create'],
                    'names' => [
                              'index'=>'indexWorker',
                              'store'=>'addWorker'
                              ]
                  ]);
  Route::get('/data/head', 'HomeController@listHead')->name('listHead');
  Route::get('/data/worker', 'HomeController@dataWorker')->name('dataWorker');
});
