<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function() {
    
    Route::auth();    
    Route::get('/', 'HomeController@index');
    Route::get('/contact', 'HomeController@contact');
    Route::post('/contact', 'HomeController@sendContactEmail');
    
    Route::get('/login', 'Auth\AuthController@showLoginForm');
    Route::get('/my-profile', 'Auth\AuthController@myProfile');
    Route::get('/update-profile', 'Auth\AuthController@myProfile');
    Route::post('/update-profile', 'Auth\AuthController@updateProfile');
    Route::get('/change-password', 'Auth\AuthController@myProfile');
    Route::post('/change-password', 'Auth\AuthController@changePassword');
    Route::post('/cancel-account', 'Auth\AuthController@cancelAccount');
    
    Route::post('/payment/process-payment', 'PaymentController@processPayment');
    Route::get('/payment/process-payment-results', 'PaymentController@processPaymentResults');
    Route::post('/payment/process-payment-results', 'PaymentController@processPaymentResults');
    Route::get('/payment/process-subscription-results', 'PaymentController@processSubscriptionResults');
    Route::post('/payment/process-subscription-results', 'PaymentController@processSubscriptionResults');
    
    Route::get('/product-list/{id?}', 'RafflesController@productList');
    Route::get('/product-details/{id?}', 'RafflesController@productDetails');
    Route::get('/my-raffles', 'RafflesController@myRaffles');
    Route::get('/my-tickets', 'RafflesController@myTickets');
    Route::get('/my-transactions', 'RafflesController@myTransactions');
    Route::get('/products', 'ProductController@mainPage');
    Route::get('/confirm-email/{token}', 'Auth\AuthController@confirmEmail');
    
    Route::get('/app-config', 'AdminController@appConfig');
    Route::get('/update-config', 'AdminController@appConfig');
    Route::post('/update-config', 'AdminController@updateConfig');
    Route::get('/main-slider', 'AdminController@mainSlider');
    Route::get('/transactions/{id}', 'AdminController@transactionsLog');
    Route::get('/users', 'AdminController@usersList');
    Route::get('/users/raffles/{id}', 'AdminController@userRaffles');
    Route::get('/users/tickets/{id}', 'AdminController@userTickets');
    Route::get('/users/details/{id}', 'AdminController@userDetails');
    Route::get('/users/edit/{id}', 'AdminController@userEdit');
    Route::get('/users/edit', 'AdminController@usersList');
    Route::post('/users/edit', 'AdminController@userUpdate');
    
    Route::get('/countries', 'AdminController@countriesList');
    Route::get('/countries/delete/{id}', 'AdminController@deleteCountry');
    Route::get('/countries/new', 'AdminController@newCountry');
    Route::get('/countries/edit/{id}', 'AdminController@editCountry');
    Route::post('/countries/store', 'AdminController@storeCountry');
    
    Route::get('/countries/states/{country_id}', 'AdminController@statesList');
    Route::get('/countries/states/new/{country_id}', 'AdminController@newState');
    Route::get('/countries/states/edit/{id}', 'AdminController@editState');
    Route::post('/countries/states/store', 'AdminController@storeState');
    Route::get('/countries/states/delete/{id}', 'AdminController@deleteState');
    
    Route::get('/categories', 'CategoriesController@mainPage');
    Route::get('/product-conditions', 'ProductConditionController@mainPage');
});

Route::group(['before' => 'auth', 'prefix' => '/api/v1/'/*, 'middleware' => 'auth.basic.once'*/], function () {
    
    Route::get('contact-methods', 'ProductController@getContactMethods');
    Route::get('products/search/', 'ProductController@search');
    Route::get('products/{id?}', 'ProductController@index');
    Route::post('products', 'ProductController@store');
    Route::post('products/{id}', 'ProductController@update');
    Route::post('products/delete/{id}', 'ProductController@destroy');
    Route::get('products/my-products/{api_token}','ProductController@myProducts');
    
    Route::get('categories/search/{criteria}', 'CategoriesController@search');
    Route::get('categories/{id?}', 'CategoriesController@index');
    Route::post('categories', 'CategoriesController@store');
    Route::post('categories/{id}', 'CategoriesController@update');
    Route::post('categories/delete/{id}', 'CategoriesController@destroy');

    Route::get('product-conditions/search/{criteria}', 'ProductConditionController@search');
    Route::get('product-conditions/{id?}', 'ProductConditionController@index');
    Route::post('product-conditions', 'ProductConditionController@store');
    Route::post('product-conditions/{id}', 'ProductConditionController@update');
    Route::post('product-conditions/delete/{id}', 'ProductConditionController@destroy');
    
    Route::get('products/upload-files/{product_id}', 'ProductController@getFiles');
    Route::post('products/upload-files/{id}', 'ProductController@uploadFiles');
    Route::delete('products/upload-files/{id}', 'ProductController@destroyFile');
    Route::get('products/get-product-images/{id}', 'ProductController@getProductImages');
    
    Route::get('raffles/get-by-id/{id}', 'RafflesController@getById');
    Route::post('raffles/store/{product_id}', 'RafflesController@store');
    Route::delete('raffles/destroy/', 'RafflesController@destroy');
    Route::get('raffles/results/{id}', 'RafflesController@results');
    Route::get('raffles/get-by-product-id/{product_id?}', 'RafflesController@getVRaffleByProductId');
    Route::get('raffles/get-next-raffles', 'RafflesController@getNextRaffles');
    Route::get('raffles/get-incomming-raffles', 'RafflesController@getIncommingRaffles');
    Route::get('raffles/get-next-raffle-by-product-id', 'RafflesController@getNextRaffleByProductId');
    Route::get('raffles/search-raffle-available-numbers', 'RafflesController@searchRaffleAvailableNumbers');
    Route::post('raffles/assign-raffle-tickets', 'RafflesController@assignRaffleTickets');
    Route::post('raffles/register-tickets-request', 'RafflesController@registerTicketsRequest');
    
    Route::get('get-raffle-details', 'RafflesController@getRaffleDetails');
    Route::get('my-tickets', 'RafflesController@getMyTickets');
    Route::get('my-tickets-numbers', 'RafflesController@getMyTicketsNumbers');
    Route::get('my-transactions', 'RafflesController@getMyTransactions');
    
    Route::get('get-slider-items', 'AdminController@getSliderItems');
    Route::post('store-slider-item', 'AdminController@storeSliderItem');
    Route::post('update-slider-item', 'AdminController@updateSliderItem');
    Route::delete('delete-slider-item', 'AdminController@deleteSliderItem');
    Route::get('get-transactions-log', 'AdminController@getTransactionsLog');
    Route::get('get-user-products','AdminController@getUserProducts');
    Route::post('confirm-refund', 'AdminController@confirmRefund');
    Route::get('user-tickets', 'AdminController@getUserTickets');
    Route::get('get-user-tickets-numbers', 'AdminController@getUserTicketsNumbers');
    
    Route::get('get-countries', 'CountriesController@getCountries');
    Route::get('get-states', 'StatesController@getStates');
    
});
