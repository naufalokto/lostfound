<?php

class Auth
{
    protected static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::ensureSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
    }

    public static function logout(): void
    {
        self::ensureSession();
        session_destroy();
    }

    public static function user(): ?array
    {
        self::ensureSession();
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        $userModel = new User();
        return $userModel->find((int)$_SESSION['user_id']);
    }

    public static function check(): bool
    {
        self::ensureSession();
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int
    {
        self::ensureSession();
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function isAdmin(): bool
    {
        self::ensureSession();
        return !empty($_SESSION['is_admin']);
    }
}
