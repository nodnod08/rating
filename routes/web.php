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

$router->group(['middleware' => 'auth'], function () use ($router) {
   $router->post('/rate', [ 'uses' => 'RatingsController@rate']);
   $router->get('/ratings', [ 'uses' => 'RatingsController@showUsersRatings']);
   $router->get('/rating/{id}', [ 'uses' => 'RatingsController@showUserRatings']);
});

$router->post('/register', [ 'uses' => 'UserController@register']);
$router->post('/login', [ 'uses' => 'UserController@login']);
$router->post('/logout', [ 'uses' => 'UserController@logout']);