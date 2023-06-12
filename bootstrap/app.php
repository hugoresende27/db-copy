<?php
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;





$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $test = $_ENV['APP_NAME'];
    $response->getBody()->write("db-copy :: ".json_encode($test));
    return $response;
});

$app->get('/dev', function (Request $request, Response $response, $args) {
    $client = new Client($_ENV['MONGO_URI']);


    try {
        // Access the desired database
        $r = $client->selectDatabase($_ENV['MONGO_DB']);
        $r = $r->selectCollection('test');
        echo "<pre>";
        var_dump($r->find()->toArray());
        die();
    } catch (Exception $e) {
        var_dump(printf($e->getMessage()));
        die();
    }
});

return $app;