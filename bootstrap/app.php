<?php


use Slim\Factory\AppFactory;

//connect
require __DIR__ . '/../app/connect/SourceDB.php';
require __DIR__ . '/../app/connect/RabbitMQ.php';

//controllers
require __DIR__ . '/../app/http/Controllers/HomeController.php';
require __DIR__ . '/../app/http/Controllers/RabbitMQController.php';

//interface
require __DIR__ . '/../app/http/Repositories/SourceRepositoryInterface.php';

//repositories
require __DIR__ . '/../app/http/Repositories/SourceRepository.php';
require __DIR__ . '/../app/http/Repositories/MongoRepository.php';





$app = AppFactory::create();



//routes
$routes = require __DIR__ . '/../app/route.php';
$routes($app);
$GLOBALS['routesList'] = $app->getRouteCollector()->getRoutes();
return $app;