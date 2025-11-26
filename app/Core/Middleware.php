<?php

class Middleware
{
    public static function auth(): void
    {
        if (!Auth::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Unauthorized']);
            exit;
        }
    }

    public static function admin(): void
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Forbidden']);
            exit;
        }
    }
}
