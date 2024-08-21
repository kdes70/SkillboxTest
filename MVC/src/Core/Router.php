<?php

namespace App\Core;

use App\Core\Contracts\RouterInterface;
use RuntimeException;

class Router implements RouterInterface
{
    private array $routes = [];

    public function get(string $uri, array $handler): void
    {
        $this->addRoute('GET', $uri, $handler);
    }

    public function post(string $uri, array $handler): void
    {
        $this->addRoute('POST', $uri, $handler);
    }

    private function addRoute(string $method, string $uri, array $handler): void
    {
        $pattern = $this->convertUriToRegex($uri);

        $this->routes[$method][$pattern] = $handler;
    }

    private function convertUriToRegex(string $uri): string
    {
        $pattern = preg_replace('/\//', '\\/', $uri);
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?<$1>[^/]+)', $pattern);
        return "/^{$pattern}$/";
    }

    public function dispatch(Container $container): ?string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] as $pattern => $handler) {
            if (preg_match($pattern, $uri, $matches)) {
                [$controller, $controllerMethod] = $handler;
                $parameters = $this->extractParameters($matches);
                $controller = $container->get($controller);

                return $controller->$controllerMethod(...$parameters);
            }
        }

        return null;
    }

    private function extractParameters(array $matches): array
    {
        $parameters = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $parameters[$key] = $value;
            }
        }
        return $parameters;
    }
}