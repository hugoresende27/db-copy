<?php


use Http\Controllers\HomeController;
use Slim\Factory\AppFactory;

//controllers
require __DIR__ . '/../app/http/Controllers/HomeController.php';
//interface
require __DIR__ . '/../app/http/Repositories/SourceRepositoryInterface.php';
//repositories
require __DIR__ . '/../app/http/Repositories/SourceRepository.php';
require __DIR__ . '/../app/http/Repositories/MongoRepository.php';


//connect
require __DIR__ . '/../app/connect/SourceDB.php';

$app = AppFactory::create();

//error log
$app->addErrorMiddleware(true,true,true);




$app->get('/docs', [HomeController::class, 'docs']);
$app->get('/', [HomeController::class, 'index']);
$app->get('/hello/{name}', [HomeController::class, 'hello']);
$app->get('/mongo', [HomeController::class, 'mongoConnect']);

//source routes
$app->post('/source', [HomeController::class, 'sourceConnect']);
$app->post('/source/{table}', [HomeController::class, 'sourceConnectTable']);
$app->post('/source/{table}/{page}', [HomeController::class, 'sourceConnectTable']);
$app->post('/source-copy/{table}', [HomeController::class, 'copySourceTableToMongo']);

return $app;