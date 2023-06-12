<?php

use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

if (!function_exists('dd')) {
    #[NoReturn] function dd(...$vars): void
    {
        foreach ($vars as $var) {
            dump($var);
        }
        die(1);
    }
}

if (!function_exists('createResponse')) {
    function createResponse(Response $response, $data = "no data", $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

if (!function_exists('createResponseArray')) {
    function createResponseArray(Response $response, $data = "no data", $status = 200): Response
    {
        $final = [];
        foreach ($data as $d)
        {
            $final[] = json_encode($d);
        }
        $response->getBody()->write(json_encode($final));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}

if (!function_exists('getRequest')) {
    function getRequest(Request $request)
    {
        $jsonData = $request->getBody()->getContents();
        return json_decode($jsonData, true);
    }
}
