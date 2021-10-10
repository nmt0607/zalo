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
Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/register', 'App\Http\Controllers\AuthController@register');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', 'App\Http\Controllers\UserController@index');
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
    Route::get('/posts', 'App\Http\Controllers\PostController@index');
    Route::get('/posts/{id}', 'App\Http\Controllers\PostController@show');
    Route::post('/posts', 'App\Http\Controllers\PostController@store');
    Route::put('/posts/{id}', 'App\Http\Controllers\PostController@update');
    Route::delete('/posts/{id}', 'App\Http\Controllers\PostController@destroy');
});
