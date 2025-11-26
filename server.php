<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Auth.php';
require_once __DIR__ . '/app/Core/Middleware.php';
require_once __DIR__ . '/app/Core/Router.php';

// Simple autoloader for Models / Controllers / Core classes
spl_autoload_register(function (string $class): void {
    $base = __DIR__ . '/app/';
    $paths = [
        $base . 'Models/' . $class . '.php',
        $base . 'Controllers/' . $class . '.php',
        $base . 'Core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Serve static assets from /public (CSS, JS, images)
if (str_starts_with($uri, '/public/') && file_exists(__DIR__ . $uri)) {
    return false; // let PHP built-in server serve the file directly
}

// API routes (JSON for Postman / frontend)
if (str_starts_with($uri, '/api/')) {
    header('Content-Type: application/json');
    $router = new Router();
    $router->dispatch($method, $uri);
    exit;
}

// Frontend static HTML (views)
if ($uri === '' || $uri === '/' || $uri === '/home' || $uri === '/home/index.html') {
    $file = __DIR__ . '/views/home/index.html';
} elseif ($uri === '/login' || $uri === '/auth/login.html') {
    $file = __DIR__ . '/views/auth/login.html';
} elseif ($uri === '/register' || $uri === '/auth/register.html') {
    $file = __DIR__ . '/views/auth/register.html';
} elseif ($uri === '/reports' || $uri === '/reports/' || $uri === '/reports/index.html') {
    $file = __DIR__ . '/views/reports/index.html';
} elseif ($uri === '/reports/create_report.html' || $uri === '/reports/create_report') {
    $file = __DIR__ . '/views/reports/create_report.html';
} elseif ($uri === '/reports/show.html' || str_starts_with($uri, '/reports/show')) {
    $file = __DIR__ . '/views/reports/show.html';
} elseif (str_starts_with($uri, '/dashboard') || $uri === '/dashboard/user.html') {
    $file = __DIR__ . '/views/dashboard/user.html';
} else {
    $file = __DIR__ . '/views/home/index.html';
}

if (file_exists($file)) {
    readfile($file);
} else {
    http_response_code(404);
    echo '404 Not Found';
}
