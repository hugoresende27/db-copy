<?php

use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface as Response;

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
