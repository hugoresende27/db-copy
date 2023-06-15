<?php


use Slim\Factory\AppFactory;

$app = AppFactory::create();

//helpers
require_once __DIR__ . '/../app/helpers.php';

//load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../public/');
$dotenv->load();

//routes
$routes = require __DIR__ . '/../app/route.php';
$routes($app);

//globals
$GLOBALS['routesList'] = $app->getRouteCollector()->getRoutes();

return $app;