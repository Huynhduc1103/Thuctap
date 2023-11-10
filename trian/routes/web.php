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

$router->get('/search-user', 'SearchController@searchByUser');
$router->get('/search-template', 'SearchController@searchByTemplade');
$router->get('/search-event', 'SearchController@searchByEvent');
$router->get('/search-logs', 'SearchController@searchByLogs');
$router->get('/search-failed', 'SearchController@searchByFailed');
$router->get('/search', 'SearchController@searchAll');

$router->get('/pagintion-user', 'PaginationController@PagintionUser');
$router->get('/pagintion-template', 'PaginationController@PagintionTemplate');
$router->get('/pagintion-logs', 'PaginationController@PagintionLogs');
$router->get('/pagintion-event', 'PaginationController@PagintionEvent');
$router->get('/pagintion-failed', 'PaginationController@PagintionFailed');

//user
$router->get('/read-users', 'UserController@readUser');
$router->post('/create-user', 'UserController@createUser');
$router->delete('/delete-user', 'UserController@deleteUser');
$router->put('update-user', 'UserController@updateUser');
$router->get('/search-user', 'UserController@searchUsers');
$router->get('find-user', 'UserController@findId');

//logs
$router ->get('/read-logs', 'LogsController@read');
$router ->get('/find-logs','LogsController@findId');
$router->post('/craete-logs','LogsController@create');
$router->put('/update-logs','LogsController@update');
$router->delete('/delete-logs','LogsController@delete');

//envets
$router->get('read-event', 'EventController@read');
$router->get('find-event', 'EventController@findId');
$router->post('create-event', 'EventController@create');
$router->put('update-event', 'EventController@update');
$router->delete('delete-event', 'EventController@delete');

//template
$router->get('read-temp', 'TemplateController@read');
$router->get('find-temp', 'TemplateController@findId');
$router->post('create-temp', 'TemplateController@create');
$router->put('update-temp', 'TemplateController@update');
$router->delete('delete-temp', 'TemplateController@delete');

//failed
$router->get('/read-failed', 'FailedController@readFailed');
$router->post('/create-failed', 'FailedController@createFailed');
$router->get('find-failed', 'FailedController@findId');
$router->delete('/delete-failed', 'FailedController@deleteFailed');
$router->put('update-failed', 'FailedController@updateFailed');

$router->get('send-list-up', 'ExampleController@sendlistup'); 
$router->get('retry', 'retrycontroller@retry');
$router->get('send-type', 'sendType@sendType'); // hang loat theo loai
