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

Route::get('/', 'ShopController@list');

Route::prefix('cart')->group( function() {
    Route::post('/', 'CartController@post');
    Route::post('/add-to-cart', 'CartController@addToCart');
    Route::get('/', 'CartController@index');
});


Route::prefix('shop')->group( function() {
    Route::get('/', 'ShopController@list');
    Route::get('/category/{id}', 'ShopController@get_child_catalogs');
    Route::get('/{id}', 'ShopController@get_product');
});

Route::post('search', 'SearchController@search');

Route::post('filter', 'PropertyController@filter');

Auth::routes();
Route::post('/users/logout', 'Auth\LoginController@logout')->name('user.logout');

Route::get('/test', function () {return view('test');} );

Route::prefix('admin')->group( function() {
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/logout', 'Auth\AdminLoginController@adminLogout')->name('admin.logout');

    //Admin password reset
    Route::post('/password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('/password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('/password/reset', 'Auth\AdminResetPasswordController@reset');
    Route::get('/password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');


    Route::get('/categories', 'AdminCategoriesController@list')->name('admin.categories');
    Route::delete('/categories/{id}', 'AdminCategoriesController@delete');
    Route::get('/add-category', 'AdminCategoriesController@showEditForm')->name('add-category');
    Route::post('/categories', 'AdminCategoriesController@update');
});
