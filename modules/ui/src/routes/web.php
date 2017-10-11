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

Route::get('/home/docker/containers', 'DockerController@getContainers')->name('containers');
Route::get('/home/docker/container/{id}', 'DockerController@getContainer')->name('container');
Route::get('/home/docker/container/create', 'DockerController@createContainerPage')->name('createContainerPage');
Route::post('/home/docker/container/create', 'DockerController@createContainer')->name('createContainer');
Route::post('/home/docker/container/start', 'DockerController@startContainer')->name('startContainer');
Route::post('/home/docker/container/stop', 'DockerController@stopContainer')->name('stopContainer');
Route::post('/home/docker/container/pause', 'DockerController@pauseContainer')->name('pauseContainer');
