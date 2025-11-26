<?php

class ClaimController extends Controller
{
    private Claim $claims;
    private Report $reports;

    public function __construct()
    {
        $this->claims  = new Claim();
        $this->reports = new Report();
    }

    // POST /api/claims
    public function store(): void
    {
        $user = $this->requireAuth();
        $data = $this->input();

        $reportId = (int)($data['report_id'] ?? 0);
        $answer   = trim($data['answer_text'] ?? '');

        $report = $this->reports->find($reportId);
        if (!$report || $report['type'] !== 'FOUND') {
            $this->json(['message' => 'Invalid report'], 422);
        }

        if ($report['user_id'] === $user['id']) {
            $this->json(['message' => 'Cannot claim your own report'], 422);
        }

        $id = $this->claims->create([
            'report_id'   => $reportId,
            'claimer_id'  => $user['id'],
            'answer_text' => htmlspecialchars($answer, ENT_QUOTES, 'UTF-8'),
            'status'      => 'PENDING',
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->json(['id' => $id], 201);
    }

    // POST /api/claims/{id}/approve
    public function approve(int $id): void
    {
        $user = $this->requireAuth();

        $claim = $this->claims->find($id);
        if (!$claim) {
            $this->json(['message' => 'Claim not found'], 404);
        }

        $report = $this->reports->find($claim['report_id']);
        if (!$report || $report['user_id'] !== $user['id']) {
            $this->json(['message' => 'Forbidden'], 403);
        }

        $this->claims->update($id, [
            'status'     => 'APPROVED',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->reports->update($report['id'], [
            'status'     => 'SOLVED',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->json(['message' => 'Claim approved']);
    }

    // POST /api/claims/{id}/reject
    public function reject(int $id): void
    {
        $user = $this->requireAuth();

        $claim = $this->claims->find($id);
        if (!$claim) {
            $this->json(['message' => 'Claim not found'], 404);
        }

        $report = $this->reports->find($claim['report_id']);
        if (!$report || $report['user_id'] !== $user['id']) {
            $this->json(['message' => 'Forbidden'], 403);
        }

        $this->claims->update($id, [
            'status'     => 'REJECTED',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->json(['message' => 'Claim rejected']);
    }
}
