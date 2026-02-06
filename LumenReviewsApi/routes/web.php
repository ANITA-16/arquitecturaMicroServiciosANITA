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
    return $router->app->version();
});


$router->get('/reviews', 'ReviewController@index');
$router->post('/reviews', 'ReviewController@store');
$router->get('/reviews/{review}', 'ReviewController@show');
$router->put('/reviews/{review}', 'ReviewController@update');
$router->patch('/reviews/{review}', 'ReviewController@update');
$router->delete('/reviews/{review}', 'ReviewController@destroy');