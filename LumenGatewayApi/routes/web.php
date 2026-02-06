<?php

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

    /**
     * Authors routes
     */
    $router->get('/authors', 'AuthorController@index');
    $router->post('/authors', 'AuthorController@store');
    $router->get('/authors/{author}', 'AuthorController@show');
    $router->put('/authors/{author}', 'AuthorController@update');
    $router->patch('/authors/{author}', 'AuthorController@update');
    $router->delete('/authors/{author}', 'AuthorController@destroy');

    /**
     * Books routes
     */
    $router->get('/books', 'BookController@index');
    $router->post('/books', 'BookController@store');
    $router->get('/books/{book}', 'BookController@show');
    $router->put('/books/{book}', 'BookController@update');
    $router->patch('/books/{book}', 'BookController@update');
    $router->delete('/books/{book}', 'BookController@destroy');

    

    // ... rutas existentes de authors y books ...

    /**
     * Reviews routes
     */
    $router->get('/reviews', 'ReviewController@index');
    $router->post('/reviews', 'ReviewController@store');
    $router->get('/reviews/{review}', 'ReviewController@show');
    $router->put('/reviews/{review}', 'ReviewController@update');
    $router->patch('/reviews/{review}', 'ReviewController@update');
    $router->delete('/reviews/{review}', 'ReviewController@destroy');