<?php


use Slim\Factory\AppFactory;

$app = AppFactory::create();

//routes
$routes = require __DIR__ . '/../app/route.php';
$routes($app);

//globals
$GLOBALS['routesList'] = $app->getRouteCollector()->getRoutes();

return $app;