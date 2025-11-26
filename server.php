<?php

require_once __DIR__ . '/config/config.php';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static assets from /public (CSS, JS, images)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // let PHP built-in server serve the file directly
}

// Normalize base path (if hosted in subdirectory)
$basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
    if ($uri === false) {
        $uri = '/';
    }
}

// Map routes to static HTML views in /views
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
