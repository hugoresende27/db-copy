<?php


use http\Controllers\HomeController;
use http\Controllers\RabbitMQController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Slim\App;

return function (App $app) {
    //error log
    $app->addErrorMiddleware(true,true,true);


    $app->get('/', [HomeController::class, 'index']);
    $app->get('/docs', [HomeController::class, 'docs']);
    $app->get('/hello/{name}', [HomeController::class, 'hello']);
    $app->get('/mongo', [HomeController::class, 'mongoConnect']);

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