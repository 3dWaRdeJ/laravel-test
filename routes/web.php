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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();
Route::get('/apiToken', function () {
    return response(['api_token' => auth()->user()->api_token] , 200, ['Content-Type' => 'application/json']);
})->middleware('auth');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/position', 'HomeController@position');
