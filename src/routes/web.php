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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'healthz'], function () use ($router) {
    $router->get('', 'Controller@healthz');
});

$router->group(['prefix' => 'product'], function () use ($router) {
    $router->get('/search/{type}/{category}', 'ProductController@search');
    $router->get('/brands/{brand}', 'ProductController@brands');
    $router->post('/buy', 'ProductController@buy');
});
