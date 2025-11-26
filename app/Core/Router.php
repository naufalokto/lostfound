<?php

class Router
{
    private array $routes;

    public function __construct()
    {
        // routes array dari config/routes.php
        $this->routes = require __DIR__ . '/../../config/routes.php';
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $path   = rtrim($path, '/') ?: '/';

        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo json_encode(['message' => 'Route not found']);
            return;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            // ubah placeholder {id} menjadi regex grup
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
            $pattern = '#^' . rtrim($pattern, '/') . '$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // buang full match
                $controllerName = $handler['controller'];
                $methodName     = $handler['method'];

                $this->call($controllerName, $methodName, $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['message' => 'Route not found']);
    }

    private function call(string $controllerName, string $method, array $params = []): void
    {
        $controllerFile = __DIR__ . '/../Controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo json_encode(['message' => 'Controller not found']);
            return;
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            http_response_code(500);
            echo json_encode(['message' => 'Method not found']);
            return;
        }

        call_user_func_array([$controller, $method], $params);
    }
}

