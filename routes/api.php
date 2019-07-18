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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather','API\WeathersController@index');
Route::get('/weather/temperature','API\WeathersController@temperature');
Route::post('/weather','API\WeathersController@store');
Route::put('/weather','API\WeathersController@update');
Route::delete('/erase','API\WeathersController@destroy');
Route::get('/weather/locations','API\WeathersController@locations');