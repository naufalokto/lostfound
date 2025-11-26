<?php

class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    // POST /api/auth/register
    public function register(): void
    {
        $data = $this->input();

        if (empty($data['name']) || empty($data['password'])) {
            $this->json(['message' => 'wajib nama dan password'], 422);
        }

        if ($this->users->findByEmail($data['email'])) {
            $this->json(['message' => 'email sudah terdaftar'], 422);
        }

        $id = $this->users->create([
            'name' => htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8'),
            'email' => strtolower($data['email']),
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'is_admin' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $user = $this->users->find($id);
        Auth::login($user);

        unset($user['password']);
        $this->json(['user' => $user], 201);
    }

    // POST /api/auth/login
    public function login(): void
    {
        $data = $this->input();

        $user =$this->users->findByEmail($data['email'] ?? '');
        if (!$user || !password_verify($data['password'], $user['password'])) {
            $this->json(['message' => 'email atau password salah'], 401);
        }

        Auth::login($user);
        unset($user['password']);
        $this->json(['user' => $user]);
    }

    // POST /api/auth/logout
    public function logout(): void
    {
        Auth::logout();
        $this->json(['message' => 'Berhasil Logout']);
    }

    // GET /api/auth/me
    public function me(): void
    {
       $user = Auth::user();
       if (!$user) {
        $this->json(['user' => null]);
       }
       unset($user['password']);
       $this->json(['user' => $user]);
    }
}