<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware
{
    /**
     * Middleware process method
     */
    public function process(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');

        if ($apiKey !== 'secret123') {
            $response->getBody()->write('Unauthorized');
            return $response->withStatus(401);
        }

        return $next($request, $response);
    }
}
