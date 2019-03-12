<?php
Auth::routes();



Route::get('/log', ['as'=> 'log', 'uses' => 'RegisterSaleController@event_log']);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'RegisterSaleController@index');
    Route::post('/register_sale', ['as'=> 'register_sale', 'uses' => 'RegisterSaleController@register_sale']);
    Route::post('/register_questionnaire', ['as'=> 'register_questionnaire', 'uses' => 'RegisterSaleController@register_questionnaire']);

    Route::get('/test', ['as'=> 'test', 'uses' => 'RegisterSaleController@esign_test']);

    Route::get('/reg_successfull', ['as'=> 'reg_successfull', 'uses' => 'RegisterSaleController@reg_successfull']);
    Route::get('/download_document', ['as'=> 'download_document', 'uses' => 'RegisterSaleController@download_document']);

    Route::get('/download_finished_document_background', ['as'=> 'download_finished_document_background', 'uses' => 'RegisterSaleController@download_finished_document_background']);

    Route::get('admin/users/profile', ['as'=> 'admin.users.profile', 'uses' => 'Admin\UserController@profile']);
    Route::patch('admin/users/update_profile', ['as'=> 'admin.users.update_profile', 'uses' => 'Admin\UserController@update_profile']);
    Route::get('admin/sales', ['as'=> 'admin.sales.index', 'uses' => 'Admin\SaleController@index']);
    Route::post('admin/sales', ['as'=> 'admin.sales.store', 'uses' => 'Admin\SaleController@store']);
    Route::put('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::patch('admin/sales/{sales}', ['as'=> 'admin.sales.update', 'uses' => 'Admin\SaleController@update']);
    Route::delete('admin/sales/{sales}', ['as'=> 'admin.sales.destroy', 'uses' => 'Admin\SaleController@destroy']);
    Route::get('admin/sales/{sales}', ['as'=> 'admin.sales.show', 'uses' => 'Admin\SaleController@show']);
    Route::get('admin/sales/{sales}/edit', ['as'=> 'admin.sales.edit', 'uses' => 'Admin\SaleController@edit']);

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
