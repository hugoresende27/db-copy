<?php

namespace http\Repositories;

use connect\SourceDB;
use PDO;

class SourceRepository implements SourceRepositoryInterface
{

    /**
     * @param $host
     * @param $user
     * @param $pass
     * @param $dbName
     * @return array
     */
    public function readDB($host, $user, $pass, $dbName): array
    {
        $source = new SourceDB($host, $user, $pass, $dbName);
        $source = $source->connect();

        $stmt = $source->prepare("SHOW TABLES;");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return [
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'db' => $dbName,
            'tables'  => $tables
        ];
    }

    public function readTable($host, $user, $pass, $dbName, $tableName): array
    {
        $source = new SourceDB($host, $user, $pass, $dbName);
        $source = $source->connect();
        $sql = "SELECT * FROM ".$tableName.";";
        $stmt = $source->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'db' => $dbName,
            'table'  => $tableName,
            'results' => $results
        ];
    }
}