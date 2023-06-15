<?php

namespace App\test;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;


class RouteTest extends  TestCase
{
    protected $app;
    protected $requestFactory;

    public function setUp(): void
    {
        $this->app = require_once __DIR__ . '/../../bootstrap/app.php';
        $this->requestFactory = new ServerRequestFactory();
    }

    public function testVersion() {
        $this->get('/version');
        $this->assertEquals(200, $this->response->status());
        $this->assertEquals($this->app->config('version'), $this->response->body());
    }


//    public function testRouteIndexGet()
//    {
//
//        $request = $this->requestFactory->createServerRequest('GET', '/');
//
//        // Run the application
//        $response = $this->app->handle($request);
//
//        $this->assertSame(200, $response->getStatusCode());
//
//        // Assert that the response body contains the content of the .env file
//        $envContent = file_get_contents(__DIR__ . '/../../public/.env');
//        $this->assertStringContainsString('APP_NAME=', $envContent);
//    }


    public function testRouteDocs()
    {

        $request = $this->requestFactory->createServerRequest('GET', '/docs');

        // Run the application and get the response
        $response = $this->app->handle($request);


        // Assert that the response status code is 200
        $this->assertSame(200, $response->getStatusCode());

        // Assert that the response body contains the expected data
        $responseData = json_decode((string) $response->getBody(), true);

        $this->assertArrayHasKey('app', $responseData);
        $this->assertArrayHasKey('endpoints', $responseData);
        $this->assertEquals('welcome to db-copy app, made by Hugo Resende', $responseData['app']);
        // Add additional assertions for the 'endpoints' array if needed
    }






}