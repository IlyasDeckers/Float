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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/consul', 'HomeController@consul')->name('consul');
Route::get('/home/logs', 'HomeController@logs')->name('logs');

Route::get('/home/docker/containers', 'Docker\ContainerController@containersPage')->name('containers');

Route::get('/home/docker/container/create', 'Docker\ContainerController@createPage')->name('createContainerPage');
Route::post('/home/docker/container/create', 'Docker\ContainerController@createContainer')->name('createContainer');

Route::get('/home/docker/container/{id}', 'Docker\ContainerController@containerPage')->name('container');
Route::post('/home/docker/container/start', 'Docker\ContainerController@startContainer')->name('startContainer');
Route::post('/home/docker/container/stop', 'Docker\ContainerController@stopContainer')->name('stopContainer');
Route::post('/home/docker/container/pause', 'Docker\ContainerController@pauseContainer')->name('pauseContainer');

Route::get('/home/docker/images', 'Docker\ImageController@imagesPage')->name('images');
