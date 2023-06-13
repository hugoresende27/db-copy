<?php
namespace consumers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
require __DIR__. '/../../vendor/autoload.php';

class RabbitMQConsumer
{

    private AMQPStreamConnection $connection;


    public function __construct()
    {

        $this->connection = new AMQPStreamConnection('db-copy_rabbitmq', '5672', 'guest', 'guest');
    }

    public function consume(): void
    {

        $channel = $this->connection->channel();
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
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


}


$receiver = new RabbitMQConsumer();
$receiver->consume();