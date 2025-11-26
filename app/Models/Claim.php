<?php

class Claim extends Model
{
    protected string $table = 'claims';

    public function byReport(int $reportId): array
    {
        $stmt = $this->query(
            'SELECT c.*, u.name as claimer_name 
             FROM claims c 
             JOIN users u ON u.id = c.claimer_id 
             WHERE c.report_id = :rid 
             ORDER BY c.created_at DESC',
            ['rid' => $reportId]
        );
        return $stmt->fetchAll();
    }

    public function forFinder(int $userId): array
    {
        $sql = 'SELECT c.*, r.title, r.type 
                FROM claims c 
                JOIN reports r ON r.id = c.report_id 
                WHERE r.user_id = :uid 
                ORDER BY c.created_at DESC';
        $stmt = $this->query($sql, ['uid' => $userId]);
        return $stmt->fetchAll();
    }

    public function forClaimer(int $userId): array
    {
        $sql = 'SELECT c.*, r.title, r.type 
                FROM claims c 
                JOIN reports r ON r.id = c.report_id 
                WHERE c.claimer_id = :uid 
                ORDER BY c.created_at DESC';
        $stmt = $this->query($sql, ['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
