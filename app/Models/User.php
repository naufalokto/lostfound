<?php

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->query('SELECT * FROM users WHERE email = :email LIMIT 1', [
            'email' => $email,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function isProfileComplete(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;
        return !empty($user['nim']) && !empty($user['major']) && !empty($user['phone']);
    }
}
