<?php
namespace Http\Controllers;

use AllowDynamicProperties;
use connect\SourceDB;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;

class HomeController
{


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function docs(Request $request, Response $response): Response
    {
        $data = [
            'welcome to db-copy app, made by Hugo Resende',
            'endpoints' => [
                'HomeController' => [
                    '/' => 'index',
                    '/hello/{name}' => 'hello'
                ]
            ]
        ];
        createResponse($response,$data);
        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        return createResponse($response,$_ENV);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function hello(Request $request, Response $response, $args): Response
    {
        $name = $args['name']; // Retrieve the value of the "name" parameter
        $string = 'Hello '.$name.' !!!';
        return createResponse($response, $string);
    }

    public function mongoConnect(Request $request, Response $response)
    {
        $client = new Client($_ENV['MONGO_URI']);
        try {
            // Access the desired database
            $database = $client->selectDatabase($_ENV['MONGO_DB']);
            $collections = $database->listCollections();

            $collectionNames = [];
            foreach ($collections as $collection) {
                $collectionNames[] = $collection->getName();
            }

            return createResponse($response, $collectionNames);


        } catch (Exception $e) {
            dd(printf($e->getMessage()));

        }

    }

    public function sourceConnect(Request $request, Response $response)
    {

        $jsonData = $request->getBody()->getContents();
        $data = json_decode($jsonData, true);
        $host = $data['host'];
        $user = $data['user'];
        $pass = $data['pass'];
        $dbName = $data['db'];

        $source = new SourceDB($host,$user,$pass,$dbName);
        $source = $source->connect();
        $sql = "SHOW TABLES;";
        $stmt = $source->prepare($sql);
        $stmt->execute();
        $resFinal = ($stmt->fetchAll(PDO::FETCH_ASSOC));
        return createResponse($response, $resFinal);
    }
}