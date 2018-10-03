<?php
Auth::routes();

Route::get('/', 'RegisterSaleController@index');
Route::post('/register_sale', ['as'=> 'register_sale', 'uses' => 'RegisterSaleController@register_sale']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('admin/users/profile', ['as'=> 'admin.users.profile', 'uses' => 'Admin\UserController@profile']);
    Route::patch('admin/users/update_profile', ['as'=> 'admin.users.update_profile', 'uses' => 'Admin\UserController@update_profile']);
    Route::get('admin/sales', ['as'=> 'admin.sales.index', 'uses' => 'Admin\SaleController@index']);
    Route::post('admin/sales', ['as'=> 'admin.sales.store', 'uses' => 'Admin\SaleController@store']);
    Route::put('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::patch('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::delete('admin/sales/{sales}', ['as'=> 'admin.sales.destroy', 'uses' => 'Admin\SaleController@destroy']);
    Route::get('admin/sales/{sales}', ['as'=> 'admin.sales.show', 'uses' => 'Admin\SaleController@show']);
    Route::get('admin/sales/{sales}/edit', ['as'=> 'admin.sales.edit', 'uses' => 'Admin\SaleController@edit']);
    Route::get('/regSuccessfull', function () { return view('regSuccessfull', [] );
    });
});


Route::group(['namespace'=>'Admin', 'prefix' => 'admin', 'as'=>'admin.'], function(){
    Auth::routes();

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/', 'SaleController@index');
    });

    Route::group(['middleware' => ['auth', 'role:admin']], function () {
        Route::resource('users', 'UserController');
    });
});
