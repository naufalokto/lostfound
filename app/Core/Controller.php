<?php

class Controller
{
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }
        return $_POST;
    }

    protected function requireAuth(): array
    {
        $user = Auth::user();
        if (!$user) {
            $this->json(['message' => 'Unauthorized'], 401);
        }
        return $user;
    }

    protected function requireAdmin(): array
    {
        $user = $this->requireAuth();
        if (!$user['is_admin']) {
            $this->json(['message' => 'Forbidden'], 403);
        }
        return $user;
    }
}
