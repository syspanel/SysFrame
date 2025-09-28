<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\UserService;

class UserController
{
    private UserService $service;

    /**
     * Constructor with DI
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Show user by ID
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $params): ResponseInterface
    {
        $id = (int)($params['id'] ?? 0);
        $user = $this->service->find($id);

        if (!$user) {
            $response->getBody()->write("User not found");
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($user));
        return $response;
    }
}
