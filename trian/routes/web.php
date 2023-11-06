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

$router->get('testmail', 'ExampleController@testmail');
$router->get('sendall', 'ExampleController@sendall');
$router->get('read', 'indexcontroller@read');
$router->get('find', 'indexcontroller@findId');
$router->post('create', 'indexcontroller@create');
$router->put('update/{id}', 'indexcontroller@update');
$router->delete('delete/{id}', 'indexcontroller@delete');


$router->get('/searchuser', 'SearchController@searchByUser');
$router->get('/searchtemplate', 'SearchController@searchByTemplade');
$router->get('/searchevent', 'SearchController@searchByEvent');
$router->get('/searchlogs', 'SearchController@searchByLogs');
$router->get('/searchfailed', 'SearchController@searchByFailed');
$router->get('/search', 'SearchController@searchAll');

$router->get('/pagintionuser', 'PaginationController@PagintionUser');
$router->get('/pagintiontemplate', 'PaginationController@PagintionTemplate');
$router->get('/pagintionmessage', 'PaginationController@PagintionMessage');
$router->get('/pagintionlogs', 'PaginationController@PagintionLogs');
$router->get('/pagintionevent', 'PaginationController@PagintionEvent');


$router->get('/sendsms', 'sendSMS@sendSms');


//user
$router->get('/readuser', 'UserController@readUser');
$router->post('/createuser', 'UserController@createUser');
$router->delete('/deleteuser/{id}', 'UserController@deleteUser');
$router->put('updateuser/{id}', 'UserController@updateUser');
$router->get('/searchuser', 'UserController@searchUsers');

//logs
$router ->get('/readlogs', 'LogsController@read');
$router ->get('/findlogs','LogsController@findId');
$router->post('/craetelogs','LogsController@create');
$router->put('/updatelogs/{id}','LogsController@update');
$router->delete('/deletelogs/{id}','LogsController@delete');

//envets
$router->get('readevent', 'EventController@read');
$router->get('findevent', 'EventController@findId');
$router->post('createevent', 'EventController@create');
$router->put('updateevent/{id}', 'EventController@update');
$router->delete('deleteevent/{id}', 'EventController@delete');

//template
$router->get('readtemp', 'TemplateController@read');
$router->get('findtemp', 'TemplateController@findId');
$router->post('createtemp', 'TemplateController@create');
$router->put('updatetemp/{id}', 'TemplateController@update');
$router->delete('deletetemp/{id}', 'TemplateController@delete');

//message
$router->get('/readMessage', 'MessageController@readMessage');
$router->post('/createMessage', 'MessageController@createMessage');
$router->delete('/deleteMessage/{id}', 'MessageController@deleteMessage');
$router->put('updateMessage/{id}', 'MessageController@updateMessage');


//failed
$router->get('/readFailed', 'FailedController@readFailed');
$router->post('/createFailed', 'FailedController@createFailed');
$router->delete('/deleteFailed/{id}', 'FailedController@deleteFailed');
$router->put('updateFailed/{id}', 'FailedController@updateFailed');

$router->get('sendlistup', 'ExampleController@sendlistup'); 
$router->get('retry', 'retrycontroller@retry');
$router->get('sendtype', 'sendType@sendType'); // hang loat theo loai
