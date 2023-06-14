<?php


use App\http\Controllers\HomeController;
use App\http\Controllers\MongoDBController;
use App\http\Controllers\RabbitMQController;

use Slim\App;

return function (App $app) {
    //error log
    $app->addErrorMiddleware(true,true,true);


    $app->get('/', [HomeController::class, 'index']);
    $app->get('/docs', [HomeController::class, 'docs']);
    $app->get('/hello/{name}', [HomeController::class, 'hello']);
    $app->get('/mongo', [MongoDBController::class, 'mongoConnect']);
    $app->get('/mongo/{collection}', [MongoDBController::class, 'mongoGetCollection']);
    $app->get('/mongo/{collection}/{limit}', [MongoDBController::class, 'mongoGetCollection']);

    //source routes
    $app->post('/source', [HomeController::class, 'sourceConnect']);
    $app->post('/source/{table}', [HomeController::class, 'sourceConnectTable']);
    $app->post('/source/{table}/{page}', [HomeController::class, 'sourceConnectTable']);
    $app->post('/source-copy/{table}', [HomeController::class, 'copySourceTableToMongo']);

    //rabbitMQ routes
    $app->get('/rabbitmq', [RabbitMQController::class, 'testConnection']);
    $app->get('/rabbitmq-publish', [RabbitMQController::class, 'publish']);
    $app->post('/rabbitmq', [RabbitMQController::class, 'sendMessage']);

    $app->get('/dev', [HomeController::class, 'dev']);

};