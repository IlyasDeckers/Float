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

Route::get('/', 'HomeController@index')->name('home');
Route::get('/consul', 'HomeController@consul')->name('consul');
Route::get('/logs', 'HomeController@logs')->name('logs');

Route::get('/docker/containers', 'Docker\ContainerController@containersPage')->name('containers');

Route::get('/docker/container/create', 'Docker\ContainerController@createPage')->name('createContainerPage');
Route::post('/docker/container/create', 'Docker\ContainerController@createContainer')->name('createContainer');

Route::get('/docker/container/{id}', 'Docker\ContainerController@containerPage')->name('container');
Route::post('/docker/container/start', 'Docker\ContainerController@startContainer')->name('startContainer');
Route::post('/docker/container/stop', 'Docker\ContainerController@stopContainer')->name('stopContainer');
Route::post('/docker/container/pause', 'Docker\ContainerController@pauseContainer')->name('pauseContainer');

Route::get('/docker/images', 'Docker\ImageController@imagesPage')->name('images');

Route::get('/proxy', 'Proxy\ProxyController@index')->name('proxy');
Route::get('/proxy/vhost', 'Proxy\ProxyController@getVhost')->name('vhost');