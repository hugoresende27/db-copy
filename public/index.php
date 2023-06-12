<?php



/**
 * Autoload global dependencies and allow for auto-loading local dependencies via use
 */
require __DIR__. '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
//helpers
require_once __DIR__ . '/../app/helpers.php';

/**
 * Boot up application, AKA Turn the lights on.
 */
$app = require __DIR__. '/../bootstrap/app.php';



/**
 * Passing our request through the app
 */
$app->run();