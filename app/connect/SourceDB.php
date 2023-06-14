<?php

namespace App\connect;
use PDO;
class SourceDB
{
    private string $host;
    private string $user;
    private string $pass;
    private string $dbName;

    public function __construct(string $host, string $user, string $pass, string $dbName)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbName = $dbName;
    }

    public function connect(): PDO
    {
        $conn_str = "mysql:host=$this->host;dbname=$this->dbName";
        $conn = new PDO($conn_str, $this->user, $this->pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }

}