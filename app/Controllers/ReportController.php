<?php

class ReportController extends Controller
{
    private Report $reports;
    private Category $categories;

    public function __construct()
    {
        $this->reports    = new Report();
        $this->categories = new Category();
    }

    // GET /api/reports
    public function index(): void
    {
        $filters = [
            'type'        => $_GET['type']        ?? null,
            'q'           => $_GET['q']           ?? null,
            'category_id' => $_GET['category_id'] ?? null,
        ];
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $items = $this->reports->search($filters, $limit, $offset);

        // Privacy rule: hide phone for FOUND items in list
        foreach ($items as &$item) {
            if ($item['type'] === 'FOUND') {
                $item['phone_hidden'] = true;
            }
        }

        $this->json([
            'data'       => $items,
            'pagination' => ['page' => $page, 'per_page' => $limit],
        ]);
    }

    // GET /api/reports/latest
    public function latest(): void
    {
        $items = $this->reports->latest(8);
        $this->json(['data' => $items]);
    }

    // GET /api/reports/{id}
    public function show(int $id): void
    {
        $report = $this->reports->find($id);
        if (!$report) {
            $this->json(['message' => 'Report not found'], 404);
        }

        // For FOUND items, do not expose phone here; will be revealed via claim flow
        if ($report['type'] === 'FOUND') {
            unset($report['phone']);
        }

        $this->json(['data' => $report]);
    }

    // POST /api/reports/lost
    public function storeLost(): void
    {
        $user = $this->requireAuth();
        if (!$this->reportsUserCanPost($user['id'])) {
            $this->json(['message' => 'Profile incomplete'], 422);
        }

        $data = $this->input();

        $id = $this->reports->create([
            'user_id'    => $user['id'],
            'type'       => 'LOST',
            'title'      => htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
            'description'=> htmlspecialchars($data['description'] ?? '', ENT_QUOTES, 'UTF-8'),
            'category_id'=> $data['category_id'] ?? null,
            'location'   => htmlspecialchars($data['location'] ?? '', ENT_QUOTES, 'UTF-8'),
            'image_path' => $data['image_path'] ?? null,
            'status'     => 'ACTIVE',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->json(['id' => $id], 201);
    }

    // POST /api/reports/found
    public function storeFound(): void
    {
        $user = $this->requireAuth();
        if (!$this->reportsUserCanPost($user['id'])) {
            $this->json(['message' => 'Profile incomplete'], 422);
        }

        $data = $this->input();

        $id = $this->reports->create([
            'user_id'             => $user['id'],
            'type'                => 'FOUND',
            'title'               => htmlspecialchars($data['title'] ?? '', ENT_QUOTES, 'UTF-8'),
            'description'         => htmlspecialchars($data['description'] ?? '', ENT_QUOTES, 'UTF-8'),
            'category_id'         => $data['category_id'] ?? null,
            'location'            => htmlspecialchars($data['location'] ?? '', ENT_QUOTES, 'UTF-8'),
            'image_path'          => $data['image_path'] ?? null,
            'status'              => 'ACTIVE',
            'verification_question'=> $data['verification_question'] ?? null,
            'created_at'          => date('Y-m-d H:i:s'),
        ]);

        $this->json(['id' => $id], 201);
    }

    // GET /api/dashboard/reports (F-18)
    public function myReports(): void
    {
        $user = $this->requireAuth();
        $items = $this->reports->userReports($user['id']);
        $this->json(['data' => $items]);
    }

    // DELETE /api/reports/{id}
    public function destroy(int $id): void
    {
        $user = $this->requireAuth();
        $report = $this->reports->find($id);
        if (!$report || $report['user_id'] !== $user['id']) {
            $this->json(['message' => 'Forbidden'], 403);
        }

        $stmt = $this->reports->query('DELETE FROM reports WHERE id = :id', ['id' => $id]);
        $this->json(['deleted' => $stmt->rowCount() > 0]);
    }

    private function reportsUserCanPost(int $userId): bool
    {
        $userModel = new User();
        return $userModel->isProfileComplete($userId);
    }
}
