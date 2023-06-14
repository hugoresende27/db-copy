<?php

namespace App\http\Controllers;

use App\http\Repositories\MongoRepository;
use MongoDB\Client;
use MongoDB\Driver\Exception\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MongoDBController
{
    private mixed $mongoDBName;
    private MongoRepository $mongoRepository;
    private Client $client;

    public function __construct()
    {
        $this->client = new Client($_ENV['MONGO_URI']);
        $this->mongoDBName = $_ENV['MONGO_DB'];;
        $this->mongoRepository = new MongoRepository($this->client);
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

    public function mongoGetCollection(Request $request, Response $response, $args)
    {

        $fields = getRequest($request);
        $fields = $fields['fields'] ?? [];
        try {
            $collection = $this->mongoRepository->findDocument($this->mongoDBName, $args['collection'], $args['limit'] ?? 100, $fields);
            return createResponse($response, $collection);
        } catch (Exception $e) {
            dd(printf($e->getMessage()));
        }

    }


}