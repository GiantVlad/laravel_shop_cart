<?php

declare(strict_types=1);

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'ShopController@list')->name('home');

Route::prefix('cart')->group(static function(Router $router) {
    $router->post('/change-shipping', 'CartController@changeShipping')->name('cart.change_shipping');
    $router->post('/change-payment', 'CartController@changePayment')->name('cart.change_payment');
    $router->post('/add-related', 'CartController@addRelated')->name('cart.add_related');
    $router->post('/add-to-cart', 'CartController@addToCart')->name('cart.add_to_cart');;
    $router->post('/remove-item', 'CartController@removeItem')->name('cart.remove_item');;
    $router->get('/', 'CartController@index')->name('get.cart');
    $router->get('/content', 'CartController@cartContent')->name('get.cart_content');
});

Route::prefix('checkout')->group( function() {
    Route::post('/', 'CheckoutController@sendPayment')->name('post.checkout');
    Route::post('/success', 'CheckoutController@success')->name('checkout.success');
    Route::get('/success', function () {
        return view('shop.order-success');
    });
});

Route::get('/orders', 'OrderController@list')->name('orders');
Route::get('/order/{id}', 'OrderController@getOrder')->name('order');
Route::get('/order-data/{id}', 'OrderController@getOrderData')->name('order-data');
Route::post('/order/action', 'OrderController@doAction')->name('change.order.status');

Route::prefix('shop')->group( function() {
    Route::get('/', 'ShopController@list')->name('shop');
    Route::get('/category/{id}', 'ShopController@getChildCatalogs');
    Route::get('/{id}', 'ShopController@getProduct')->name('product');
});

Route::get('search', 'SearchController@search')->name('search');
Route::get('filter', 'SearchController@filter')->name('filter');

Auth::routes();

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

    //Admin categories management
    Route::get('/categories', 'AdminCategoriesController@list')->name('admin.categories');
    Route::delete('/categories/{id}', 'AdminCategoriesController@delete');
    Route::get('/add-category', 'AdminCategoriesController@showEditForm')->name('add-category');
    Route::get('/edit-category/{id}', 'AdminCategoriesController@showEditForm');
    Route::post('/categories', 'AdminCategoriesController@update');

    //Admin products management
    Route::get('/products', 'AdminProductsController@list')->name('admin.products');
    Route::delete('/products', 'AdminProductsController@delete')->name('product.delete');
    Route::get('/add-product', 'AdminProductsController@showEditForm')->name('add-product');
    Route::get('/edit-product/{id}', 'AdminProductsController@showEditForm');
    Route::post('/products', 'AdminProductsController@update');
    Route::get('/products/category/{id}', 'AdminProductsController@categoryFilter');

    //Admin add and remove products properties
    Route::delete('/product/{product_id}/property', 'AdminProductsController@deleteProperty');
    Route::get('/product/{product_id}/properties', 'AdminProductsController@getProperties');

    Route::get('/products/property-types', 'AdminPropertiesController@getProperties');
    Route::get('/products/property/{id}/values', 'AdminPropertiesController@getPropertyValues');
    Route::post('/product/property-type', 'AdminPropertiesController@addPropertyToProduct');
    Route::post('/properties', 'AdminPropertiesController@createProperty');

    Route::get('/users', 'AdminUsersController@list')->name('admin.users');
    Route::get('/edit-user/{id}', 'AdminUsersController@showEditForm');
    Route::put('/user', 'AdminUsersController@update')->name('user.update');
    Route::post('/users', 'AdminUsersController@search')->name('users.search');
    Route::put('/users/', 'AdminUsersController@deleteCart')->name('cart.delete');

    Route::get('/orders', 'AdminOrdersController@list')->name('admin.orders');
    Route::get('/show-order/{id}', 'AdminOrdersController@showEditForm');
    Route::put('/order', 'AdminOrdersController@update')->name('order.update');
    Route::post('/orders', 'AdminOrdersController@search')->name('order.search');

    Route::get('/shipping-methods', 'AdminShippingMethodsController@list')->name('admin.shipping-methods');
    Route::put('/shipping-method', 'AdminShippingMethodsController@changeStatus');
});

Route::get('/test', 'TestController@test');

Route::get('/debug-sentry', function () {
    throw new Exception('My Sentry error!');
});

//Route::get('paywithpaypal', ['as' => 'paywithpaypal', 'uses' => 'PaypalController@payWithPaypal',]);
//Route::post('paypal', ['as' => 'paypal', 'uses' => 'PaypalController@postPaymentWithpaypal',]);
//Route::get('paypal', ['as' => 'status', 'uses' => 'PaypalController@getPaymentStatus',]);
Route::get('paypal', ['as' => 'paywithpaypal', 'uses' => 'PaypalController2@pay',]);
