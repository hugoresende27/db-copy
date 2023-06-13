<?php

namespace http\Controllers;
use http\Repositories\MongoRepository;
use http\Repositories\SourceRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;

class HomeController
{

    private MongoRepository $mongoRepository;
    private Client $client;
    private mixed $mongoDBName;

    public function __construct()
    {
        $this->client = new Client($_ENV['MONGO_URI']);
        $this->mongoDBName = $_ENV['MONGO_DB'];;
        $this->mongoRepository = new MongoRepository($this->client);
    }

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

        try {
            $collectionNames = $this->mongoRepository->getCollectionNames($this->mongoDBName);
            return createResponse($response, $collectionNames);
        } catch (Exception $e) {
            dd(printf($e->getMessage()));
        }

    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function sourceConnect(Request $request, Response $response): Response
    {
        $credentials = getRequest($request);
        $source = new SourceRepository();
        $finalData = $source->readDB($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db']);
        return createResponse($response, $finalData);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $agrs
     * @return Response
     */
    public function sourceConnectTable(Request $request, Response $response, $agrs): Response
    {
        $credentials = getRequest($request);
        $source = new SourceRepository();
        $page = 1;
        if (isset($agrs['page'])) {
            $page = $agrs['page'];
        }
        $finalData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $agrs['table'], $page);

        if(json_encode($finalData['results'])){
            return createResponse($response, $finalData['results']);
        } else {
            return createResponseArray($response, $finalData['results']);
        }
    }



    public function copySourceTableToMongo(Request $request, Response $response, $table): Response
    {
        $credentials = getRequest($request);
        $copyDbName = $credentials['copy_db_name'];
        $source = new SourceRepository();
        $finalData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $table['table']);

        //create mongoDB database
        $databaseCreated = $this->mongoRepository->createDatabase($copyDbName);
        // Check if the collection exists
        $collectionExists = $this->mongoRepository->collectionExists($copyDbName, $table['table']);
        // Delete the collection if it exists
        if ($collectionExists) {
            $this->mongoRepository->deleteCollection($copyDbName, $table['table']);
        }
        //create mongoDB collection
        $collectionCreated = $this->mongoRepository->addCollection($copyDbName, $table['table']);

        // Get the MongoDB collection object
        $collection = $this->mongoRepository->getCollection($copyDbName, $table['table']);

        $totalRecords = 0;
        foreach ($finalData['results'] as $result) {
            foreach ($result as $key => $value) {
                if (is_string($value)) {
                    // Fix UTF-8 encoding or remove invalid characters
                    $result[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }
            }

            //insert record in mongoDB
            $collection->insertOne($result);
            $totalRecords++;
        }

        $finalRes = [
            'database_created' => $databaseCreated,
            'collection_created' => $collectionCreated,
            'total_records' => $totalRecords
        ];

        return createResponse($response, $finalRes);
    }


}