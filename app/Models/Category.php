<?php

class Category extends Model
{
    protected string $table = 'categories';

    public function allActive(): array
    {
        $stmt = $this->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }
}
