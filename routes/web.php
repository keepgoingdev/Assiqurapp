<?php
Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::resource('customers', 'Admin\CustomerController');
    Route::get('/', 'RegisterSaleController@index')->name('registerSale');
});

Route::group(['namespace'=>'Admin', 'prefix' => 'admin', 'as'=>'admin.'], function(){
    Auth::routes();
    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        Route::resource('users', 'UserController');
        Route::get('/', 'CustomerController@index');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('customers', 'CustomerController');
    });
});


