<?php

namespace App\test;

use App\http\Controllers\HomeController;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RouteTest extends  TestCase
{
    protected $app;

    public function setUp(): void
    {
        $this->app = require __DIR__. '/../../bootstrap/app.php';
    }

    public function testIndexRoute()
    {
        $homeController = new HomeController();
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);

        $returnedResponse = $homeController->index($request, $response);

        // Perform assertions on the returned response
        $this->assertSame(200, $returnedResponse->getStatusCode());
        $this->assertSame("Expected response body", (string) $returnedResponse->getBody());
    }


}