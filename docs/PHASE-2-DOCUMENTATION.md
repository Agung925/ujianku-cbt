# PHASE 2: Authentication & Authorization

Completion Date: 2026-05-08
Status: COMPLETE

## Overview
Phase 2 mengimplementasikan 3 alur autentikasi berbeda sesuai role:
- Guru: Google OAuth (Socialite)
- Siswa: NIS + Password (session-based)
- Admin/Super Admin: Email + Password (guard web)

## Sub-Prompt 2.1 - Google OAuth Guru

Implemented:
- `app/Http/Controllers/Auth/GoogleCallbackController.php`
- OAuth routes:
  - `GET /auth/google` (`google.redirect`)
  - `GET /auth/google/callback` (`google.callback`)
- DB migration:
  - `database/migrations/2026_05_08_071722_add_google_id_to_gurus_table.php`

Behavior:
- Redirect ke provider Google
- Callback memvalidasi tenant context
- Sinkronisasi data guru berdasarkan email
- Simpan `google_id` + update avatar
- Auto-create akun user untuk guru jika belum ada
- Assign role `guru` jika belum ada
- Login via guard `web`, redirect ke dashboard guru

## Sub-Prompt 2.2 - Login Siswa (NIS + Password)

Implemented:
- `app/Http/Requests/Auth/SiswaLoginRequest.php`
- `app/Http/Controllers/Auth/SiswaAuthController.php`
- `resources/views/auth/siswa-login.blade.php`
- Middleware session siswa: `app/Http/Middleware/IsSiswa.php`

Routes:
- `GET /siswa/login` (`siswa.login`)
- `POST /siswa/login` (`siswa.login.store`)
- `POST /siswa/logout` (`siswa.logout`)

Behavior:
- Validasi NIS pada tenant aktif
- Validasi password dengan `Hash::check`
- Simpan sesi custom `siswa_auth`
- Timeout sesi siswa 2 jam di middleware `IsSiswa`

## Sub-Prompt 2.3 - Login Admin/Super Admin

Implemented:
- `app/Http/Requests/Auth/AdminLoginRequest.php`
- `app/Http/Controllers/Auth/AdminAuthController.php`
- `resources/views/auth/admin-login.blade.php`

Routes:
- `GET /admin/login` (`admin.login`)
- `POST /admin/login` (`admin.login.store`)
- `POST /admin/logout` (`admin.logout`)

Behavior:
- Login via `Auth::attempt`
- Role validation: hanya `admin` atau `super_admin`
- Redirect role-based ke dashboard yang sesuai

## Sub-Prompt 2.4 - Role-Based Middleware & Wiring

Implemented middleware:
- `app/Http/Middleware/CheckRole.php`
- `app/Http/Middleware/CheckTenant.php`
- `app/Http/Middleware/IsSiswa.php`
- `app/Http/Middleware/IsAdmin.php`
- `app/Http/Middleware/IsAdminOrSuperAdmin.php`

Bootstrap registration:
- `bootstrap/app.php`
  - aliases: `checkRole`, `checkTenant`, `siswa.auth`, `isAdmin`, `isAdminOrSuperAdmin`
  - route-group protection:
    - `super-admin`: `web, auth, checkRole:super_admin`
    - `admin`: `web, auth, checkTenant, checkRole:admin`
    - `guru`: `web, auth, checkTenant, checkRole:guru`
    - `siswa`: `web, checkTenant, siswa.auth`

## Updated Existing Files
- `routes/auth.php` (semua endpoint auth role-based)
- `resources/views/auth/login.blade.php` (button/login path tambahan)
- `app/Models/Guru.php` (`google_id` fillable)
- `.env.example` (Google OAuth env keys)

## Verification

Executed:
- `php artisan migrate --force` (migration `add_google_id_to_gurus_table` sukses)
- `php artisan route:list` validasi route baru (`admin/login`, `siswa/login`, `auth/google`, logout endpoints)

Note:
- `php artisan test` gagal karena environment tidak memiliki SQLite driver (`could not find driver`), bukan karena logic Phase 2.

## Result
Phase 2 selesai dengan alur autentikasi multi-role aktif dan middleware tenant/role sudah terpasang.
