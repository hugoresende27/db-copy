<?php

namespace http\Repositories;
use MongoDB\Client;
class MongoRepository
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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

        $this->client->createDatabase($databaseName);
        return true;
    }


}