<?php

use DI\ContainerBuilder;
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Create the container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$app = AppFactory::createFromContainer($container);

//error log
$app->addErrorMiddleware(true,true,true);

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {

    $name = $args['name']; // Retrieve the value of the "name" parameter

    $response->getBody()->write('Hello '.$name.'!');
    return $response;
});

$app->get('/', function (Request $request, Response $response) {
    $test = $_ENV['APP_NAME'];
    $response->getBody()->write("db-copy :: ".json_encode($test));
    return $response;
});

$app->get('/dev', function () {
    $client = new Client($_ENV['MONGO_URI']);


    try {
        // Access the desired database
        $r = $client->selectDatabase($_ENV['MONGO_DB']);
        $r = $r->selectCollection('test');
        echo "<pre>";
        dd($r->find()->toArray());

    } catch (Exception $e) {
        dd(printf($e->getMessage()));

    }
});


class UserController
{

    public function index()
    {
        dd('aqui');
    }
}

$app->get('/c', [UserController::class, 'index']);

return $app;