<?php

namespace App\connect;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQ
{
    public mixed $rabbitMQHost;
    public mixed $rabbitMQPort;
    public mixed $rabbitMQUser;
    public mixed $rabbitMQPassword;
    public string $rabbitMQQueueTests;
    public string $rabbitMQQueueSQL;
    public AMQPStreamConnection $connection;

    public function __construct()
    {
        // Initialize RabbitMQ connection settings
        $this->rabbitMQHost = $_ENV['MQ_HOST'];
        $this->rabbitMQPort = $_ENV['MQ_PORT'];
        $this->rabbitMQUser = $_ENV['MQ_USER'];
        $this->rabbitMQPassword = $_ENV['MQ_PASS'];
        $this->rabbitMQQueueTests = 'tests'; // Queue name for testing purposes
        $this->rabbitMQQueueSQL = 'sql'; // Queue name for sql purposes
        $this->connection = new AMQPStreamConnection($this->rabbitMQHost, $this->rabbitMQPort, $this->rabbitMQUser, $this->rabbitMQPassword);
    }
}