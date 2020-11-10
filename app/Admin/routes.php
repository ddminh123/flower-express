<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UserController::class);
    $router->resource('customers', CustomerController::class);
    $router->resource('categories', CategoryController::class);
    $router->resource('invoices', InvoiceController::class);
    $router->resource('products', ProductController::class);
    $router->resource('invoice-details', InvoiceDetailController::class);
    $router->get('florist', 'InvoiceController@florist')->name('florist');
    $router->get('shipper', 'InvoiceController@shipper')->name('shipper');
});
