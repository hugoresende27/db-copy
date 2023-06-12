<?php


use Http\Controllers\HomeController;
use Slim\Factory\AppFactory;

//controllers
require __DIR__ . '/../app/http/Controllers/HomeController.php';

//connect
require __DIR__ . '/../app/connect/SourceDB.php';

$app = AppFactory::create();

//error log
$app->addErrorMiddleware(true,true,true);




$app->get('/docs', [HomeController::class, 'docs']);
$app->get('/', [HomeController::class, 'index']);
$app->get('/hello/{name}', [HomeController::class, 'hello']);
$app->get('/mongo', [HomeController::class, 'mongoConnect']);
$app->post('/source', [HomeController::class, 'sourceConnect']);

return $app;