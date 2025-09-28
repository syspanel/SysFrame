<?php

namespace SysFrame;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private array $routes = [];
    private ContainerInterface $container;
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddlewares = [];

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Map route to one or multiple HTTP methods
     */
    public function map(array $methods, string $path, $handler, array $middlewares = []): void
    {
        $path = $this->currentGroupPrefix . $path;
        $middlewares = array_merge($this->currentGroupMiddlewares, $middlewares);

        $this->routes[] = [
            'methods' => $methods,
            'path' => $this->convertPathToRegex($path),
            'raw_path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Define a group of routes with a common prefix and middlewares
     */
    public function group(string $prefix, callable $callback, array $middlewares = []): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddlewares = $this->currentGroupMiddlewares;

        $this->currentGroupPrefix .= $prefix;
        $this->currentGroupMiddlewares = array_merge($this->currentGroupMiddlewares, $middlewares);

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddlewares = $previousMiddlewares;
    }

    /**
     * Dispatch request and return response
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $method = $request->getMethod();
        $uri = $request->getUri()->getPath();

        foreach ($this->routes as $route) {
            if (in_array($method, $route['methods']) && preg_match($route['path'], $uri, $matches)) {
                array_shift($matches); // remove full match
                $dispatcher = new MiddlewareDispatcher($route['middlewares'], $route['handler'], $this->container);
                return $dispatcher->handle($request, $response, $matches);
            }
        }

        $response->getBody()->write("404 Not Found");
        return $response->withStatus(404);
    }

    /**
     * Convert route path with parameters to regex
     */
    private function convertPathToRegex(string $path): string
    {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $path);
        return '#^' . $regex . '$#';
    }
}
