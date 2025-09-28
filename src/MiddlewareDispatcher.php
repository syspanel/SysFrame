<?php

namespace SysFrame;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareDispatcher
{
    private array $middlewares;
    private $handler;
    private ContainerInterface $container;

    /**
     * Constructor
     */
    public function __construct(array $middlewares, $handler, ContainerInterface $container)
    {
        $this->middlewares = $middlewares;
        $this->handler = $handler;
        $this->container = $container;
    }

    /**
     * Handle request and execute middlewares in order
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $routeParams = []): ResponseInterface
    {
        $middleware = array_shift($this->middlewares);

        if ($middleware === null) {
            return $this->invokeHandler($request, $response, $routeParams);
        }

        if (is_string($middleware)) {
            $middleware = $this->container->get($middleware);
        }

        if (is_object($middleware) && method_exists($middleware, 'process')) {
            return $middleware->process($request, $response, function ($req, $res) use ($routeParams) {
                return $this->handle($req, $res, $routeParams);
            });
        }

        if (is_callable($middleware)) {
            return $middleware($request, $response, function ($req, $res) use ($routeParams) {
                return $this->handle($req, $res, $routeParams);
            });
        }

        throw new \Exception("Invalid middleware type");
    }

    /**
     * Invoke the final route handler
     */
    private function invokeHandler(ServerRequestInterface $request, ResponseInterface $response, array $routeParams): ResponseInterface
    {
        $handler = $this->handler;

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            $instance = $this->container->get($class);
            return $instance->$method($request, $response, $routeParams);
        }

        if (is_string($handler)) {
            $instance = $this->container->get($handler);
            return $instance($request, $response, $routeParams);
        }

        if (is_callable($handler)) {
            return $handler($request, $response, $routeParams);
        }

        throw new \Exception("Invalid route handler");
    }
}
