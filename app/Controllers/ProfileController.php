<?php

class ProfileController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    // GET /api/profile
    public function show(): void
    {
        $user = $this->requireAuth();
        unset($user['password']);

        $this->json([
            'user'            => $user,
            'profile_complete'=> $this->users->isProfileComplete($user['id']),
        ]);
    }

    // PUT /api/profile
    public function update(): void
    {
        $user = $this->requireAuth();
        $data = $this->input();

        $update = [
            'name'  => htmlspecialchars($data['name'] ?? $user['name'], ENT_QUOTES, 'UTF-8'),
            'nim'   => $data['nim']   ?? $user['nim'],
            'major' => $data['major'] ?? $user['major'],
            'phone' => $data['phone'] ?? $user['phone'],
        ];

        $this->users->update($user['id'], $update);

        $fresh = $this->users->find($user['id']);
        unset($fresh['password']);

        $this->json([
            'user'            => $fresh,
            'profile_complete'=> $this->users->isProfileComplete($user['id']),
        ]);
    }
}
