<?php
namespace consumers;
require __DIR__. '/../../vendor/autoload.php';
require __DIR__. '/../../app/http/Repositories/MongoRepository.php';
use http\Repositories\MongoRepository;
use MongoDB\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;



class RabbitMQConsumer
{

    private AMQPStreamConnection $connection;
    private Client $client;
    private mixed $mongoDBName;
    private MongoRepository $mongoRepository;


    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('db-copy_rabbitmq', '5672', 'guest', 'guest');
        $this->client = new Client('mongodb://172.22.0.2:27017/');
        $this->mongoDBName = 'copy_db';
        $this->mongoRepository = new MongoRepository($this->client);
    }

    public function consume(): void
    {

        $channel = $this->connection->channel();
        $callback = function ($msg) {
            $this->insertRecord($msg->body);
        };
        $channel->queue_declare('tests', false, false, false, false);
        $channel->basic_consume('tests', '', false, true, false, false, $callback);
        echo 'Waiting for new message on tests', " \n";

        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $this->connection->close();
    }

    public function insertRecord($record): void
    {

        echo ' [x] Message: ', $record, "\n";
        $arrayData = json_decode($record, true);
        // Get the MongoDB collection object
        $collection = $this->mongoRepository->getCollection($this->mongoDBName, $arrayData['source_table']);
        $collection->insertOne($arrayData);


    }


}


$receiver = new RabbitMQConsumer();
$receiver->consume();