<?php

require __DIR__ . '/../vendor/autoload.php';

use SysFrame\Application;
use App\Middlewares\AuthMiddleware;
use App\Controllers\UserController;
use DI\ContainerBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;

// Build PHP-DI container
$builder = new ContainerBuilder();
$container = $builder->build();

// Create application
$app = new Application($container);

// Add global middleware
$app->addMiddleware(AuthMiddleware::class);

// Define route
$app->get('/user/{id:\d+}', [UserController::class, 'show']);

// PSR-7 request/response
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
);
$request = $creator->fromGlobals();
$response = $psr17Factory->createResponse();

// Run application
$response = $app->run($request, $response);

// Send response
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();
