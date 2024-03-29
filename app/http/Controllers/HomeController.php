<?php

namespace App\http\Controllers;

use App\http\Repositories\MongoRepository;
use App\http\Repositories\SourceRepository;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use MongoDB\Client;


class HomeController
{

    private MongoRepository $mongoRepository;
    private Client $client;

    private RabbitMQController $rabbitMQController;

    public function __construct()
    {
        $this->client = new Client($_ENV['MONGO_URI']);
        $this->mongoRepository = new MongoRepository($this->client);
        $this->rabbitMQController = new RabbitMQController();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function docs(Request $request, Response $response): Response
    {
        global $routesList;
        $endpoints = [];
        foreach ($routesList as $route)
        {
            $endpoints [] = $route->getPattern();
        }

        $data = [
            'app' => 'welcome to db-copy app, made by Hugo Resende',
            'endpoints' => $endpoints
        ];

        return createResponse($response,$data);
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
        ini_set('max_execution_time', 900); // Set maximum execution time

        $credentials = getRequest($request);
        $copyDbName = $credentials['copy_db_name'];
        $source = new SourceRepository();

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
//        $collection = $this->mongoRepository->getCollection($copyDbName, $table['table']);


        $totalRecords = 0;


        $finalData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $table['table']);

        $i = 1;
        while ($finalData['total_pages'] >= $i) {
            $finalData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $table['table'], $i++);
            foreach ($finalData['results'] as $result) {
                foreach ($result as $key => $value) {
                    if (is_string($value)) {
                        // Fix UTF-8 encoding or remove invalid characters
                        $result[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                    }
                }

                $result['source_table'] = $table['table'];

                //insert record in mongoDB
//            $collection->insertOne($result);
                $this->rabbitMQController->publishMessage(json_encode($result), 'dbcopy');
                $totalRecords++;
            }
        }


        $finalRes = [
            'database_created' => $databaseCreated,
            'collection_created' => $collectionCreated,
            'total_records' => $totalRecords
        ];

        return createResponse($response, $finalRes);
    }

    private function retrieveFinalData(SourceRepository $source, array $credentials, $table): array
    {
        $finalData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $table['table']);

        $i = 1;
        while ($finalData['total_pages'] >= $i) {
            $moreData = $source->readTable($credentials['host'], $credentials['user'], $credentials['pass'], $credentials['db'], $table['table'], $i++);
            $finalData['results'] = array_merge($moreData['results'], $finalData['results']);
        }

        return $finalData;
    }

    public function dev()
    {
        dd('dev');
    }
}