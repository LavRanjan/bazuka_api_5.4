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

// Route for profile management
Route::post('login', 'Api\AuthController@login'); //route for login
Route::post('register', 'Api\AuthController@register'); //route for login
Route::post('refresh', 'Api\AuthController@refresh');

Route::group(['middleware' => ['jwt.auth']], function () {

    Route::post('user', 'Api\AuthController@getAuthUser'); //

});

