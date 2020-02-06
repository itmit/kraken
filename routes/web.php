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

Route::group(['as' => 'auth.', 'middleware' => 'auth'], function () {
    
    Route::view('/', 'main');

    Route::resource('clients', 'Web\ClientWebController');

    Route::resource('masters', 'Web\MasterWebController');

    Route::resource('inquiries', 'Web\InquiryWebController');

    Route::resource('departments', 'Web\DepartmentWebController');
    
});

Auth::routes();

