<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', "Project@index");
Route::post('/truncateall', "Project@truncateall");

Route::get('/api/project.search', "Project@search");

Route::get('/api/project.names', "Project@projectNames");

Route::post('/api/project.create-or-update', "Project@createOrUpdateProject");

Route::post('/api/project.delete', "Project@deleteProject");

Route::get('/api/project.create-or-update', "Project@createOrUpdateProject");
