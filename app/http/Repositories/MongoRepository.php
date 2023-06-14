<?php

namespace App\http\Repositories;

use MongoDB\Client;
use MongoDB\Collection;

class MongoRepository
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    public function getCollection(string $databaseName, string $collectionName): Collection
    {
        return $this->client->selectCollection($databaseName, $collectionName);
    }

    public function getCollectionNames(string $databaseName): array
    {
        $database = $this->client->selectDatabase($databaseName);
        $collections = $database->listCollections();

        $collectionNames = [];
        foreach ($collections as $collection) {
            $collectionNames[] = $collection->getName();
        }

        return $collectionNames;
    }

    public function addCollection(string $databaseName, string $collectionName): bool
    {
        $database = $this->client->selectDatabase($databaseName);

        // Check if the collection already exists
        $existingCollections = iterator_to_array($database->listCollectionNames());
        if (in_array($collectionName, $existingCollections)) {
            return false; // Collection already exists
        }
        // Create the collection
        $database->createCollection($collectionName);
        return true;
    }

    public function createDatabase(string $databaseName): bool
    {
        $databases = $this->client->listDatabases();
        foreach ($databases as $database) {
            if ($database->getName() === $databaseName) {
                return false; // Database already exists
            }
        }
        $this->client->selectDatabase($databaseName);
        // The database will be created when you perform an operation on it, like inserting a document
        return true;
    }

    public function collectionExists(string $databaseName, string $collectionName): bool
    {
        $collectionList = $this->client->selectDatabase($databaseName)->listCollections();
        foreach ($collectionList as $collection) {
            if ($collection->getName() === $collectionName) {
                return true; // Collection exists
            }
        }
        return false; // Collection does not exist
    }

    public function deleteCollection(string $databaseName, string $collectionName): bool
    {
        $collection = $this->client->selectDatabase($databaseName)->selectCollection($collectionName);
        $result = $collection->drop();
        return true;
    }

    public function findDocument(string $databaseName, string $collectionName, int $limit, array $fields): array
    {
        $collection = $this->client->selectDatabase($databaseName)->selectCollection($collectionName);
        if (!empty($fields)) {
            $projectionArray = [];
            foreach ($fields as $field) {
                $projectionArray[$field] = 1;
            }

        }

        $cursor = $collection->find([], [
                'projection' => $projectionArray ?? null,
                'limit' => $limit
            ]
        );
        $results = [];
        foreach ($cursor as $document) {

            $results[] = $document;
        }
        return $results;
    }

}