<?php
// @codingStandardsIgnoreStart

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

//User Exposed End points
Route::group([
    'prefix' => 'users'

], function () {
    Route::post('register','UserController@store');
});

// User protected end points (has to be logged in to access)
Route::group([
    'prefix' => 'users', 'middleware' => ['jwt.auth']
], function () {
    Route::post('update', 'UserController@update');
    Route::get('address','UserController@getUserAddresses');
    // Route::get('show', 'UserController@show');
    Route::get('orders', 'OrderController@getUserOrders');

});



Route::group([
    'prefix' => 'suppliers'

], function () {
    Route::post('register','SupplierController@store');
});

// Protected End points, (has to be logged in to access)
Route::group(['middleware' => ['jwt.auth']],function(){
    Route::group([
        'prefix' => 'store'
    ], function () {
        Route::post('addListing','ListingController@addListing');
        Route::get('item/{key}','ListingController@getListing');
        Route::get('SupplierItems/{id}','ListingController@getSupplierListing');
        Route::get('SupplierItems/{id}','ListingController@getSupplierListing');
        Route::post('addListingImages/{key}','ListingController@addListingImages');
        Route::post('checkout','OrderController@checkOut');
        Route::get('/orders/{orderId}', 'OrderController@getOrderDetails');
        Route::post('processPayment','OrderController@processTransection');
        }
    );
});

Route::group(['middleware' => ['jwt.auth']],function(){
    Route::group([
        'prefix' => 'agent'
    ], function () {
        Route::get('orders','DeliveryController@getAgentAssignedOrders');
        Route::get('getQR/{id}','OrderController@getHalalahQRC');
        }
    );
});




