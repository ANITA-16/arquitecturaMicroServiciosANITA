<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json([
        'service' => 'Orders Service',
        'version' => $router->app->version(),
        'status' => 'running',
        'port' => 8008
    ]);
});

// Orders CRUD
$router->get('/orders', 'OrderController@index');                    // GET all orders (with optional status filter)
$router->post('/orders', 'OrderController@store');                   // POST create new order
$router->get('/orders/{id}', 'OrderController@show');               // GET specific order
$router->put('/orders/{id}', 'OrderController@update');             // PUT update order (status, address)
$router->patch('/orders/{id}', 'OrderController@update');           // PATCH update order
$router->delete('/orders/{id}', 'OrderController@destroy');         // DELETE cancel order

// User orders
$router->get('/orders/user/{user_id}', 'OrderController@getUserOrders'); // GET orders by user

// Statistics
$router->get('/orders/stats/summary', 'OrderController@statistics'); // GET order statistics