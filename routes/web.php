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


Route::post('/cart', 'CartController@post');

Route::post('/cart/add-to-cart', 'CartController@addToCart');

Route::get('/cart', 'CartController@index');

Route::get('/', 'CartController@index');

Route::get('/shop', 'ShopController@list');

Route::get('shop/{id}', 'ShopController@get_product');

Route::post('search', 'SearchController@search');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
