<?php
namespace App\http\Controllers;

use App\connect\RabbitMQ;
use DateInterval;
use DateTime;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;
class RabbitMQController
{


    private RabbitMQ $rabbitMQ;

    public function __construct()
    {
        $this->rabbitMQ = new RabbitMQ();
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function testConnection(Request $request, Response $response): Response
    {
        try {

            // Create a channel
            $channel = $this->rabbitMQ->connection->channel();

            // Declare the queue
            $channel->queue_declare($this->rabbitMQ->rabbitMQQueueTests, false, false, false, false);

            // Send a test message
            for ($i = 0 ; $i < 5 ; $i++)
            {
                $message = new AMQPMessage('--'.$i.'--'.$_ENV['APP_NAME']);
                $channel->basic_publish($message, '', $this->rabbitMQ->rabbitMQQueueTests);
            }


            // Close the channel and connection
            $channel->close();
            $this->rabbitMQ->connection->close();

            // Return a success response
            $response->getBody()->write('RabbitMQ connection test successful');
            return $response->withStatus(200);
        } catch (Exception $e) {
            // Handle the exception and return an error response
            $response->getBody()->write('Error: ' . $e->getMessage());
            return $response->withStatus(500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function publish(Request $request, Response $response)
    {
        $message = "publish function";
        $channel = $this->rabbitMQ->connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare($this->rabbitMQ->rabbitMQQueueTests, false, false, false, false);
        $channel->queue_bind($this->rabbitMQ->rabbitMQQueueTests, 'test_exchange', 'test_key');
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        echo " [x] Sent $message to test_exchange / test_queue.\n";
        $channel->close();
        $this->rabbitMQ->connection->close();
        return createResponse($response, $message);
    }

    public function publishMessage(string $message, string $exchange )
    {

        $channel = $this->rabbitMQ->connection->channel();
        $channel->exchange_declare($exchange, 'direct', false, false, false);
        $channel->queue_declare($this->rabbitMQ->rabbitMQQueueTests, false, false, false, false);
        $channel->queue_bind($this->rabbitMQ->rabbitMQQueueTests, $exchange, 'db-copy');

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, $exchange, 'db-copy');
        $channel->close();
        $this->rabbitMQ->connection->close();

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function sendMessage(Request $request,Response $response): Response
    {

        $currentTime = new DateTime();
        $currentTime->add(new DateInterval('PT1H'));//add one hour
        $currentTime = $currentTime->format('Y-m-d H:i:s');
        $message = getRequest($request);
        $finalMessage = $message['message'].' :: at '.$currentTime;

        $channel = $this->rabbitMQ->connection->channel();

        $channel->queue_declare($this->rabbitMQ->rabbitMQQueueTests, false, false, false, false);

        $msg = new AMQPMessage($finalMessage);
        $channel->basic_publish($msg, '', $this->rabbitMQ->rabbitMQQueueTests);


        $channel->close();
        $this->rabbitMQ->connection->close();
        // Return a success response
        $response->getBody()->write('message send with success :: '.$finalMessage);
        return $response->withStatus(200);
    }


}