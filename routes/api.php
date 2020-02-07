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

Route::post('login', 'Api\AuthApiController@login');
Route::post('register', 'Api\AuthApiController@register');

Route::group(['middleware' => 'auth:api'], function(){

    Route::get('getTypeOfWork', 'Api\InquiryApiController@getTypeOfWork');

    Route::post('inquiry/store', 'Api\InquiryApiController@store');

    Route::post('masters/changeStatus', 'Api\MasterApiController@changeStatus');
    Route::post('masters/updateLocation', 'Api\MasterApiController@updateLocation');

});