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

$router->get('test', 'ExampleController@test');
$router->get('read', 'indexcontroller@read');
$router->get('find', 'indexcontroller@findId');
$router->post('create', 'indexcontroller@create');
$router->put('update/{id}', 'indexcontroller@update');
$router->delete('delete/{id}', 'indexcontroller@delete');

$router->get('/searchuser', 'SearchController@searchByUser');
$router->get('/searchtemplate', 'SearchController@searchByTemplade');
$router->get('/searchtempstatus', 'SearchController@searchByStatus');
$router->get('/searchmessage', 'SearchController@searchByMessage');
$router->get('/searchlogs', 'SearchController@searchByLogs');

$router->get('/pagintionuser', 'PaginationController@PagintionUser');
$router->get('/pagintiontemplate', 'PaginationController@PagintionTemplate');
$router->get('/pagintionstatus', 'PaginationController@PagintionStatus');
$router->get('/pagintionmessage', 'PaginationController@PagintionMessage');
$router->get('/pagintionlogs', 'PaginationController@PagintionLogs');