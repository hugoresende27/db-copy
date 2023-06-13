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


    public function readTable($host, $user, $pass, $dbName, $tableName,$page = 1, $pageSize = 100): array
    {
        $source = new SourceDB($host, $user, $pass, $dbName);
        $source = $source->connect();
        $totalRows = $this->countRows($source, $tableName);
        if ($totalRows >= 100) {
            $totalPages = ceil($totalRows / $pageSize);
            $offset = ($page - 1) * $pageSize;

            $results = [];
            $sql = "SELECT * FROM {$tableName} LIMIT {$pageSize} OFFSET {$offset};";
            $stmt = $source->prepare($sql);
            $stmt->execute();

        } else {
            $sql = "SELECT * FROM {$tableName}";
            $stmt = $source->prepare($sql);
            $stmt->execute();
            $results = [];

        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }

        return [
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'db' => $dbName,
            'table' => $tableName,
            'total_pages' => $totalPages ?? $page,
            'current_page' => $page,
            'results' => $results
        ];

    }
    public function countRows(PDO $source, string $tableName): int
    {
        $sql = "SELECT COUNT(*) as total_rows FROM {$tableName}";
        $stmt = $source->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total_rows'];
    }


}