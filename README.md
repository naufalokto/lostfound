# Lost & Found API (PHP Native)

Backend JSON API untuk platform Lost & Found. Frontend (HTML/CSS/JS di folder `views/` & `public/`) akan diproses terpisah oleh tim FE – API ini siap dihubungkan ke Postman atau FE.

## 1. Setup & Base URL

- Jalankan server lokal:

```bash
cd /Users/macbook/Documents/lostandfound
php -S localhost:8000 server.php
```

- **Base URL API**: `http://localhost:8000`
- Semua endpoint API diawali dengan: `/api/...`
- Auth menggunakan **PHP session (cookie)** → setelah login, gunakan cookie yang sama untuk request berikutnya (Postman akan menyimpannya otomatis).

---

## 2. Auth & Access Control

### 2.1 Register User (F‑01)

- **POST** `/api/auth/register`
- Body (JSON):

```json
{
  "name": "Alice",
  "email": "alice@example.com",
  "password": "secret123"
}
```

- Response 201:

```json
{
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com",
    "nim": null,
    "major": null,
    "phone": null,
    "is_admin": 0,
    "created_at": "2025-11-26 10:00:00",
    "updated_at": null
  }
}
```

### 2.2 Login (F‑03)

- **POST** `/api/auth/login`
- Body:

```json
{
  "email": "alice@example.com",
  "password": "secret123"
}
```

- Response 200:

```json
{
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com",
    "nim": null,
    "major": null,
    "phone": null,
    "is_admin": 0,
    "created_at": "2025-11-26 10:00:00",
    "updated_at": null
  }
}
```

### 2.3 Logout

- **POST** `/api/auth/logout`

```json
{ "message": "Logged out" }
```

### 2.4 Current User (`/me`)

- **GET** `/api/auth/me`

```json
{
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com",
    "nim": "12345678",
    "major": "Informatika",
    "phone": "08123456789",
    "is_admin": 0,
    "created_at": "2025-11-26 10:00:00",
    "updated_at": "2025-11-26 11:00:00"
  }
}
```

Jika belum login:

```json
{ "user": null }
```

---

## 3. Profile / Account Management (F‑08, F‑09)

### 3.1 Get Profile

- **GET** `/api/profile`
- Needs login (cookie dari login).

```json
{
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com",
    "nim": "12345678",
    "major": "Informatika",
    "phone": "08123456789",
    "is_admin": 0,
    "created_at": "2025-11-26 10:00:00",
    "updated_at": "2025-11-26 11:00:00"
  },
  "profile_complete": true
}
```

Jika tidak login:

```json
{ "message": "Unauthorized" }
```

### 3.2 Update Profile

- **PUT** `/api/profile`
- Body:

```json
{
  "name": "Alice",
  "nim": "12345678",
  "major": "Informatika",
  "phone": "08123456789"
}
```

- Response:

```json
{
  "user": {
    "id": 1,
    "name": "Alice",
    "email": "alice@example.com",
    "nim": "12345678",
    "major": "Informatika",
    "phone": "08123456789",
    "is_admin": 0,
    "created_at": "2025-11-26 10:00:00",
    "updated_at": "2025-11-26 11:05:00"
  },
  "profile_complete": true
}
```

Jika data kurang valid:

```json
{ "message": "Name, email, and password are required" }
```

---

## 4. Reports (Lost / Found) – Core

### 4.1 List Reports + Search / Filter (F‑12, F‑13)

- **GET** `/api/reports`
- Query params (opsional):
  - `type` → `LOST` atau `FOUND`
  - `q` → keyword (judul/deskripsi)
  - `category_id` → id kategori
  - `page` → default 1

Contoh:

`GET /api/reports?type=FOUND&q=wallet&category_id=1&page=1`

Response:

```json
{
  "data": [
    {
      "id": 5,
      "user_id": 2,
      "type": "FOUND",
      "title": "Black Wallet",
      "description": "Found near cafeteria",
      "category_id": 1,
      "location": "Cafeteria",
      "image_path": "uploads/xyz.jpg",
      "status": "ACTIVE",
      "verification_question": "What is the color inside?",
      "created_at": "2025-11-26 11:10:00",
      "updated_at": null,
      "phone_hidden": true
    }
  ],
  "pagination": {
    "page": 1,
    "per_page": 10
  }
}
```

### 4.2 Latest Reports (Landing - F‑06)

- **GET** `/api/reports/latest`

```json
{
  "data": [
    {
      "id": 10,
      "user_id": 3,
      "type": "LOST",
      "title": "Student ID",
      "description": "Lost near Building A",
      "category_id": 3,
      "location": "Building A",
      "image_path": null,
      "status": "ACTIVE",
      "verification_question": null,
      "created_at": "2025-11-26 12:00:00",
      "updated_at": null
    }
  ]
}
```

### 4.3 Detail Report

- **GET** `/api/reports/{id}` → misalnya `/api/reports/5`

```json
{
  "data": {
    "id": 5,
    "user_id": 2,
    "type": "FOUND",
    "title": "Black Wallet",
    "description": "Found near cafeteria",
    "category_id": 1,
    "location": "Cafeteria",
    "image_path": "uploads/xyz.jpg",
    "status": "ACTIVE",
    "verification_question": "What is the color inside?",
    "created_at": "2025-11-26 11:10:00",
    "updated_at": null
  }
}
```

Jika tidak ada:

```json
{ "message": "Report not found" }
```

### 4.4 Create LOST Report (F‑11)

- **POST** `/api/reports/lost`
- Needs login + profile lengkap.
- Body:

```json
{
  "title": "Lost Backpack",
  "description": "Black backpack with laptop inside",
  "category_id": 1,
  "location": "Main Library"
}
```

Response:

```json
{ "id": 12 }
```

Jika profil belum lengkap (F‑09):

```json
{ "message": "Profile incomplete" }
```

### 4.5 Create FOUND Report (F‑10)

- **POST** `/api/reports/found`
- Body:

```json
{
  "title": "Found USB Drive",
  "description": "Blue USB drive near Lab 3",
  "category_id": 1,
  "location": "Lab 3",
  "verification_question": "What is the wallpaper on the lock screen?"
}
```

Response:

```json
{ "id": 13 }
```

### 4.6 User Dashboard – My Reports (F‑18)

- **GET** `/api/dashboard/reports`

```json
{
  "data": [
    {
      "id": 12,
      "user_id": 1,
      "type": "LOST",
      "title": "Lost Backpack",
      "description": "Black backpack with laptop inside",
      "category_id": 1,
      "location": "Main Library",
      "image_path": null,
      "status": "ACTIVE",
      "verification_question": null,
      "created_at": "2025-11-26 11:30:00",
      "updated_at": null
    }
  ]
}
```

### 4.7 Delete Report (F‑19)

- **DELETE** `/api/reports/{id}`

```json
{ "deleted": true }
```

Jika bukan pemilik:

```json
{ "message": "Forbidden" }
```

---

## 5. Claim & Verification (Found Items) – F‑14 s/d F‑17

### 5.1 Create Claim (F‑14, F‑15)

- **POST** `/api/claims`
- Body:

```json
{
  "report_id": 13,
  "answer_text": "The wallpaper is a cat on the beach"
}
```

Response:

```json
{ "id": 7 }
```

Jika mencoba claim report sendiri:

```json
{ "message": "Cannot claim your own report" }
```

### 5.2 Approve Claim (F‑16, F‑17)

- **POST** `/api/claims/{id}/approve` → contoh `/api/claims/7/approve`  
  (Finder / pemilik report FOUND harus login)

Response:

```json
{ "message": "Claim approved" }
```

Efek:
- `claims.status` → `APPROVED`
- `reports.status` → `SOLVED`

### 5.3 Reject Claim

- **POST** `/api/claims/{id}/reject`

```json
{ "message": "Claim rejected" }
```

---

## 6. Admin (F‑20, F‑21)

### 6.1 Admin Dashboard (Stat Global)

- **GET** `/api/admin/dashboard`  
  (harus login sebagai user `is_admin = 1`)

Response:

```json
{
  "stats": {
    "total_reports": 25,
    "active_reports": 18,
    "solved_reports": 5,
    "total_users": 40
  }
}
```

### 6.2 Admin Create FOUND Item (F‑21)

- **POST** `/api/admin/reports/found`
- Body:

```json
{
  "user_id": 2,
  "title": "Silver Laptop",
  "description": "Turned in at the security office",
  "category_id": 1,
  "image_path": null
}
```

Response:

```json
{ "id": 20 }
```

Lokasi akan otomatis diset sebagai **"Security Office"** di backend.

---

## 7. Catatan Tambahan (Security & Privacy)

- **Session & Auth**:
  - Gunakan endpoint login untuk mendapatkan session cookie.
  - Semua endpoint yang butuh login akan mengembalikan:

```json
{ "message": "Unauthorized" }
```

  jika cookie tidak ada / tidak valid.

- **Privacy Found Items**:
  - Endpoint list & detail report **tidak mengirim nomor telepon** untuk report `FOUND`.
  - Kontak baru dibuka setelah alur claim disetujui (bisa diimplementasi di layer notifikasi/dashboard).

- **XSS Protection**:
  - `title`, `description`, dll. di‑sanitize dengan `htmlspecialchars` sebelum disimpan.

Dengan dokumentasi ini kamu bisa:
- Import semua endpoint ke Postman (dengan base URL `http://localhost:8000`).
- Test flow penuh: register → login → lengkapi profil → post lost/found → claim → approve/reject → cek dashboard & admin stats. 


