<?php
Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'RegisterSaleController@index')->name('registerSale');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('admin/sales', ['as'=> 'admin.sales.index', 'uses' => 'Admin\SaleController@index']);
    Route::post('admin/sales', ['as'=> 'admin.sales.store', 'uses' => 'Admin\SaleController@store']);
    Route::get('admin/sales/create', ['as'=> 'admin.sales.create', 'uses' => 'Admin\SaleController@create']);
    Route::put('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::patch('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::delete('admin/sales/{sales}', ['as'=> 'admin.sales.destroy', 'uses' => 'Admin\SaleController@destroy']);
    Route::get('admin/sales/{sales}', ['as'=> 'admin.sales.show', 'uses' => 'Admin\SaleController@show']);
    Route::get('admin/sales/{sales}/edit', ['as'=> 'admin.sales.edit', 'uses' => 'Admin\SaleController@edit']);
});


Route::group(['namespace'=>'Admin', 'prefix' => 'admin', 'as'=>'admin.'], function(){
    Auth::routes();
    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        Route::resource('users', 'UserController');
        Route::get('/', 'SaleController@index');
    });
});
