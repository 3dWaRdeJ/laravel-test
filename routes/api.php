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

Route::group(['prefix' => '/v1', 'middleware' => 'auth:api'], function () {
    Route::group(['prefix' => '/employee'], function() {
        Route::get('', 'API\V1\EmployeeController@search');
        Route::post('', 'API\V1\EmployeeController@store');
        Route::put('', 'API\V1\EmployeeController@update');
        Route::delete('', 'API\V1\EmployeeController@delete');
    });
    Route::group(['prefix' => '/position'], function() {
        Route::get('', 'API\V1\PositionController@search');
        Route::post('', 'API\V1\PositionController@store');
        Route::put('', 'API\V1\PositionController@update');
        Route::delete('', 'API\V1\PositionController@delete');
    });

});
