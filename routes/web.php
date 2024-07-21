<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::group(['middleware' => 'role:admin'], function () {
    Route::get('/users', 'App\Http\Controllers\UserController@index')->name('user.index');
    Route::delete('/users/{id}', 'App\Http\Controllers\UserController@destroy')->name('users.destroy');
    Route::get('/users/{id}/show', 'App\Http\Controllers\UserController@show')->name('users.show');
    Route::get('/users/{id}/edit', 'App\Http\Controllers\UserController@edit')->name('users.edit');
    Route::put('/users/{id}', 'App\Http\Controllers\UserController@update')->name('users.update');
    Route::put('/users/{id}/role', 'App\Http\Controllers\UserController@updaterole')->name('users.updaterole');
    Route::post('/users/store', 'App\Http\Controllers\UserController@store')->name('users.store');

    Route::get('/users/downloadTemplate', 'App\Http\Controllers\UserController@downloadTemplate')->name('users.downloadTemplate');
    Route::post('/users/import', 'App\Http\Controllers\UserController@import')->name('users.import');
    Route::get('/users/export', 'App\Http\Controllers\UserController@export')->name('user.export');

    Route::get('/categories', 'App\Http\Controllers\CategoryController@index')->name('category.index');
    Route::delete('/categories/{id}', 'App\Http\Controllers\CategoryController@destroy')->name('category.destroy');
    Route::get('/categories/{id}/show', 'App\Http\Controllers\CategoryController@show')->name('category.show');
    Route::get('/categories/{id}/edit', 'App\Http\Controllers\CategoryController@edit')->name('category.edit');
    Route::put('/categories/{id}', 'App\Http\Controllers\CategoryController@update')->name('category.update');
    Route::post('/categories/store', 'App\Http\Controllers\CategoryController@store')->name('category.store');

    Route::get('/category/downloadTemplate', 'App\Http\Controllers\CategoryController@downloadTemplate')->name('category.downloadTemplate');
    Route::post('/category/import', 'App\Http\Controllers\CategoryController@import')->name('category.import');
    Route::get('/category/export', 'App\Http\Controllers\CategoryController@export')->name('category.export');
    
    Route::get('/products', 'App\Http\Controllers\ProductController@index')->name('products.index');
    Route::delete('/products/{product}', 'App\Http\Controllers\ProductController@destroy')->name('products.destroy');
    Route::post('/products/store', 'App\Http\Controllers\ProductController@store')->name('products.store');
    Route::put('/products/{product}', 'App\Http\Controllers\ProductController@update')->name('products.update');
    Route::get('/products/{product}/edit', 'App\Http\Controllers\ProductController@edit')->name('products.edit');

    Route::get('/customer', 'App\Http\Controllers\CustomerController@index')->name('customer.index');
    Route::delete('/customers/{id}', 'App\Http\Controllers\CustomerController@destroy')->name('customers.destroy');
    Route::get('/customers/{id}/show', 'App\Http\Controllers\CustomerController@show')->name('customers.show');
    Route::get('/customers/{id}/edit', 'App\Http\Controllers\CustomerController@edit')->name('customers.edit');
    Route::put('/customers/{id}', 'App\Http\Controllers\CustomerController@update')->name('customers.update');
    Route::post('/customers/store', 'App\Http\Controllers\CustomerController@store')->name('customers.store');

    Route::get('/customers/downloadTemplate', 'App\Http\Controllers\CustomerController@downloadTemplate')->name('customers.downloadTemplate');
    Route::post('/customers/import', 'App\Http\Controllers\CustomerController@import')->name('customers.import');
    Route::get('/customers/export', 'App\Http\Controllers\CustomerController@export')->name('customers.export');
// });

Route::get('/product-carts', 'App\Http\Controllers\EcommerceController@index')->name('product.index');

Route::middleware('auth')->group(function () {
    Route::post('/carts/{id}', 'App\Http\Controllers\EcommerceController@addToCart')->name('cart.addToCart');
    Route::get('/carts', 'App\Http\Controllers\EcommerceController@cart')->name('cart.cart');
    Route::post('/checkout', 'App\Http\Controllers\EcommerceController@checkout')->name('cart.checkout');
    Route::put('/carts/{id}', 'App\Http\Controllers\EcommerceController@updateCart')->name('cart.updateCart');
});