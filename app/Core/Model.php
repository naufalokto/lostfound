<?php

abstract class Model
{
    protected \PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", [
            'id' => $id,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(',', $columns),
            implode(',', $placeholders)
        );

        $this->query($sql, $data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach ($data as $col => $val) {
            $sets[] = "{$col} = :{$col}";
        }
        $data['id'] = $id;

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table,
            implode(',', $sets)
        );

        $stmt = $this->query($sql, $data);
        return $stmt->rowCount() > 0;
    }
}
