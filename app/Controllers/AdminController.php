<?php

class AdminController extends controller
{
    private Report $reports;
    private Claim $users;
    private Claim $claims;

    public function __construct()
    {
        $this->reports = new Report();
        $this->users = new User();
        $this->claims = new Claim();
    }

    // GET /api/admin/dashboard
    public function dashboard(): void
    {
        $this->requireAdmin();

        $db = Database::getInstance()->pdo();

        $totalReports = (int)$db->query('SELECT COUNT(*) FROM reports')->fetchColumn();
        $activeReports = (int)$db->query("SELECT COUNT(*) FROM reports WHERE status = 'ACTIVE'")->fetchColumn();
        $solvedReports = (int)$db->query("SELECT COUNT(*) FROM reports WHERE status = 'SOLVED'")->fetchColumn();
        $totalUsers = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();

        $this->json([
            'stats' => [
                'total_reports' => $totalReports,
                'active_reports' => $activeReports,
                'solved_reports' => $solvedReports,
                'total_users' => $totalUsers,
            ],
        ]);
    }

    // POST /api/admin/reports/found
    public function createFound(): void
    {
        $this->requireAdmin();
        $data = $this->input();

        $userId = (int)($data['user_id'] ?? 0);
        if (!$this->users->find($userId)) {
            $this->json(['message' => 'User not found'], 422);
        }

        $id = $this->reports->create([
            'user_id'    => $userId,
            'type'       => 'FOUND',
            'title'      => htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
            'description'=> htmlspecialchars($data['description'] ?? '', ENT_QUOTES, 'UTF-8'),
            'category_id'=> $data['category_id'] ?? null,
            'location'   => 'Security Office',
            'image_path' => $data['image_path'] ?? null,
            'status'     => 'ACTIVE',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->json(['id' => $id], 201);
    }
}