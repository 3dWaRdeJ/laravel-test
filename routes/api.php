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

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/v1'], function () {
    Route::group(['prefix' => '/employee', 'middleware' => 'auth:api'], function() {
        Route::get('', 'API\V1\EmployeeController@search');
        Route::post('', 'API\V1\EmployeeController@store');
        Route::put('', 'API\V1\EmployeeController@update');
        Route::delete('', 'API\V1\EmployeeController@delete');
    });
});
