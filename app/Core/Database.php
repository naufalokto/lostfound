<?php

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_PORT,
            DB_CHARSET
        );

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->pdo = new \PDO($dsn, DB_USER, DB_PASS, $options);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }
}
