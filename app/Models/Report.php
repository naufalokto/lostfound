<?php

class Report extends Model
{
    protected string $table = 'reports';

    public function latest(int $limit = 8): array
    {
        $stmt = $this->query(
            'SELECT r.*, u.name 
             FROM reports r 
             JOIN users u ON u.id = r.user_id 
             WHERE r.status = :status 
             ORDER BY r.created_at DESC 
             LIMIT :limit',
            [
                'status' => 'ACTIVE',
                'limit'  => $limit,
            ]
        );
        return $stmt->fetchAll();
    }

    public function search(array $filters, int $limit = 10, int $offset = 0): array
    {
        $sql = 'SELECT r.* FROM reports r WHERE 1=1';
        $params = [];

        if (!empty($filters['type'])) {
            $sql .= ' AND r.type = :type';
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['q'])) {
            $sql .= ' AND (r.title LIKE :q OR r.description LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $sql .= ' AND r.category_id = :category_id';
            $params['category_id'] = $filters['category_id'];
        }

        $sql .= ' ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function userReports(int $userId): array
    {
        $stmt = $this->query(
            'SELECT * FROM reports WHERE user_id = :uid ORDER BY created_at DESC',
            ['uid' => $userId]
        );
        return $stmt->fetchAll();
    }
}
