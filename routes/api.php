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

    // Route::post('inquiry/store', 'Api\InquiryApiController@store');
    Route::resource('inquiry', 'Api\InquiryApiController');

    Route::post('inquiry/masters', 'Api\InquiryApiController@getMasterList');
    Route::post('inquiry/selectMaster', 'Api\InquiryApiController@selectMaster');

    Route::get('masters/getInquiryToMasterList', 'Api\MasterApiController@getInquiryToMasterList');
    Route::get('masters/getInquiryList', 'Api\MasterApiController@getInquiryList');

    Route::post('masters/changeStatus', 'Api\MasterApiController@changeStatus');
    Route::post('masters/updateLocation', 'Api\MasterApiController@updateLocation');
    Route::post('masters/applyInquiry', 'Api\MasterApiController@applyInquiry');
    Route::post('masters/finishInquiry', 'Api\MasterApiController@finishInquiry');
    Route::post('masters/cancelInquiry', 'Api\MasterApiController@cancelInquiry');
    Route::post('masters/changeWayToTravel', 'Api\MasterApiController@changeWayToTravel');

    Route::post('updateDeviceToken', 'Api\AuthApiController@updateDeviceToken');

    Route::get('client', 'Api\ClientApiController@index');

});

Route::get('googletest', 'Api\InquiryApiController@test');