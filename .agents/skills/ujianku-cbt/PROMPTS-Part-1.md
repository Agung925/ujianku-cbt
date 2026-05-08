# 🚀 UJIANKU-CBT — Master Prompt & Sub-Prompts (Part 1)

**Untuk:** GitHub Copilot Enterprise Agent di VS Code  
**Versi:** 1.0  
**Bahasa:** Indonesian  
**Format:** Instruksi step-by-step yang langsung bisa dijalankan

---

## 📌 CARA MENGGUNAKAN PROMPT INI

1. **Buka VS Code** di folder `/workir/www/ujianku-cbt`
2. **Buka GitHub Copilot Chat** (Cmd/Ctrl + Shift + I)
3. **Paste salah satu prompt** di bawah sesuai fase
4. **Tekan Enter** dan biarkan Copilot execute
5. **Review hasil**, jika ada error → adjust & re-run

---

## 🎯 MASTER PROMPT (Baca Dulu!)

Sebelum menjalankan sub-prompts, copilot perlu tahu context lengkap:

```
Kamu adalah AI Developer Assistant untuk project Laravel multi-tenant bernama UJIANKU-CBT.

Baca file context di: .agents/skills/ujianku-cbt/SKILL.md

Dalam setiap prompt, kamu HARUS:
1. Mengikuti struktur folder yang sudah ditentukan di SKILL.md
2. Mengikuti naming convention yang sudah ditentukan
3. Menggunakan stancl/tenancy v3 untuk multi-tenant
4. Menggunakan spatie/laravel-permission untuk roles
5. Menambahkan tenant_id pada setiap tabel yang perlu
6. Menambahkan security checks di middleware
7. Menggunakan Tailwind CSS + DaisyUI untuk styling
8. Menulis kode yang readable dan well-commented (dalam Indonesian)
9. Setiap file model perlu relationship definitions
10. Setiap migration perlu foreign keys & indexes

⚠️ PENTING: GIT OPERATIONS
- ❌ JANGAN lakukan: git commit, git push, git add
- ❌ JANGAN jalankan git commands apapun
- ✅ HANYA USER yang boleh commit & push manual
- ✅ Kamu hanya siapkan kode yang siap commit
- ✅ Di akhir phase, informkan: "Kode siap commit oleh user"

Setelah selesai dengan satu task, informkan kamu sudah selesai dan siap untuk task berikutnya.

Jika ada ambiguitas, TANYAKAN kepada developer untuk konfirmasi.
```

---

# 📍 PHASE 0: Project Setup & Initial Configuration

## ⏱️ Estimasi: 30 menit | Kompleksitas: Easy | Priority: CRITICAL

Sebelum mulai development, setup project dengan dependencies dan config yang tepat.

### Sub-Prompt 0.1: Initialize Laravel Project dengan Tailwind + DaisyUI

```
Kerjakan task ini langkah demi langkah:

1. Di folder /workir/www/ujianku-cbt, pastikan sudah ada:
   - Laravel 11 (fresh install)
   - Composer dependencies
   - .env file (copy dari .env.example)

2. Install dan setup Tailwind CSS v3 + DaisyUI v4:
   - npm install -D tailwindcss postcss autoprefixer daisyui
   - npx tailwindcss init -p
   - Update tailwind.config.js dengan DaisyUI plugin
   - Setup resources/css/app.css dengan Tailwind directives

3. Setup Vite configuration untuk Tailwind:
   - Pastikan vite.config.js sudah benar import resources/css/app.css
   - Test dengan: npm run dev

4. Create folder structure di resources/views:
   - layouts/
   - components/
   - superadmin/
   - admin/
   - guru/
   - siswa/

5. Create base layout: resources/views/layouts/app.blade.php
   - Include navbar component
   - Include sidebar component
   - Main content area
   - Footer (optional)
   - DaisyUI theme selector (optional)

6. Show me the structure ketika selesai.
```

### Sub-Prompt 0.2: Install Required Laravel Packages

```
Install dan configure semua required packages:

Packages to install (via composer):
- laravel/breeze v2 (untuk auth scaffolding)
- laravel/socialite v5 (untuk Google OAuth)
- stancl/tenancy v3 (untuk multi-tenant)
- spatie/laravel-permission v6 (untuk roles & permissions)
- maatwebsite/excel v3.1 (untuk Excel import)
- intervention/image v3 (untuk image processing)

Steps:
1. Run: composer require [package names]
2. Publish config files (php artisan vendor:publish)
3. Update .env dengan required variables (jika ada)
4. Run migrations (php artisan migrate) — tapi STOP kalau ada error

Setelah selesai, informkan packages apa saja yang sudah terinstall.
```

### Sub-Prompt 0.3: Setup stancl/tenancy Configuration

```
Configure stancl/tenancy v3 untuk multi-tenant system.

Tasks:
1. Publish tenancy config:
   - php artisan tenancy:publish-config

2. Edit config/tenancy.php:
   - Set domain/path strategy (recommend: domain-based untuk SaaS)
   - Set database connection strategy (recommend: single database)
   - Configure tenant detection
   - Set cache key

3. Create first tenant migration:
   - php artisan make:migration create_tenants_table --path=database/migrations/landlord
   - Tenants table harus include: id, domain, path, name, is_active, created_at

4. Publish database migrations:
   - php artisan tenancy:publish-migrations

5. Run landlord migrations:
   - php artisan migrate --path=database/migrations/landlord

Output: Berikan saya migration file untuk tenants table yang sudah dibuat.
```

### Sub-Prompt 0.4: Setup spatie/laravel-permission

```
Configure spatie/laravel-permission untuk roles & permissions system.

Tasks:
1. Publish config:
   - php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

2. Run migrations:
   - php artisan migrate

3. Create seeder file: database/seeders/RoleAndPermissionSeeder.php
   Roles yang perlu dibuat:
   - super_admin (platform level)
   - admin (per tenant level)
   - guru
   - siswa (read-only most things)

   Permissions yang perlu dibuat:
   - manage-tenants (super_admin only)
   - manage-users
   - manage-exams
   - manage-questions
   - view-grades
   - take-exam
   - (dan permissions lainnya sesuai fitur)

4. Create seeder: database/seeders/SuperAdminSeeder.php
   - Create super admin user (email: admin@ujianku.test, password: password)
   - Assign super_admin role

5. Update User model:
   - Use HasRoles trait dari spatie
   - Add: use HasRoles;

Output: Berikan saya kode role/permission seeder yang sudah dibuat.
```

---

# 📍 PHASE 1: Database Design & Migrations

## ⏱️ Estimasi: 45 menit | Kompleksitas: Medium | Priority: CRITICAL

Buat semua migrations dan models sesuai dengan schema yang sudah direncanakan di SKILL.md.

### Sub-Prompt 1.1: Create Models & Migrations for Users & Authentication

```
Create models dan migrations untuk sistem user:

Models & Migrations to create:
1. User (super_admin, admin)
   - id, tenant_id (nullable), name, email, password, password_history, role, foto_profil, is_active, created_at, updated_at
   - Add softDelete trait
   - Relationships: hasMany('AdminProfiles')

2. Guru
   - id, tenant_id, user_id (nullable), email, nama, nip, foto_profil, is_wali_kelas, is_active, created_at, updated_at
   - Relationships: belongsTo(User), hasMany(Soal), hasMany(Ujian)

3. Siswa
   - id, tenant_id, nis (unique per tenant), nama, email (nullable), password, foto, kelas, is_active, created_at, updated_at
   - Add softDelete trait
   - Relationships: hasManyThrough(Nilai), hasManyThrough(JawabanSiswa)

Tasks:
1. Generate models dan migrations dengan: php artisan make:model [Model] -m
2. Edit migration files sesuai schema di atas
3. Add proper indexes: tenant_id, email, nis
4. Add foreign key constraints dengan onDelete('cascade')
5. Add model relationships (relationships di model, bukan di migration)
6. Run migrations: php artisan migrate

Output: Berikan saya daftar 3 migration files yang sudah dibuat.
```

### Sub-Prompt 1.2: Create Models & Migrations for Exam System

```
Create models dan migrations untuk sistem ujian:

Models & Migrations to create:
1. KategoriUjian (exam categories)
   - id, tenant_id, nama, deskripsi, urutan, is_active, created_at, updated_at
   - Relationships: hasMany(Soal), hasMany(Ujian)

2. Soal (exam questions)
   - id, tenant_id, kategori_ujian_id, guru_id, pertanyaan (text), tipe_soal (enum: pilihan_ganda/essay), opsi_a, opsi_b, opsi_c, opsi_d (nullable), kunci_jawaban, bobot (default: 1), is_active, created_at, updated_at
   - Relationships: belongsTo(KategoriUjian), belongsTo(Guru), hasMany(JawabanSiswa)

3. Ujian (exam sessions)
   - id, tenant_id, guru_id, kategori_ujian_id, judul, deskripsi, tgl_mulai, tgl_selesai, waktu_durasi (menit), is_acak_soal, is_acak_opsi, is_active, created_at, updated_at
   - Relationships: belongsTo(Guru), hasMany(JawabanSiswa), hasMany(Nilai)

4. JawabanSiswa (student answers)
   - id, tenant_id, ujian_id, siswa_id, soal_id, jawaban (text), waktu_mulai, waktu_selesai, is_submitted, created_at, updated_at
   - Relationships: belongsTo(Ujian), belongsTo(Siswa), belongsTo(Soal)

5. Nilai (grades)
   - id, tenant_id, ujian_id, siswa_id, nilai_otomatis (float, nullable), nilai_essay (float, nullable), nilai_akhir (float, computed), status (enum: lulus/tidak_lulus), catatan_guru, created_at, updated_at
   - Relationships: belongsTo(Ujian), belongsTo(Siswa)

Tasks:
1. Generate models & migrations
2. Edit migrations dengan schema di atas
3. Add foreign keys dengan proper cascading
4. Add indexes: tenant_id, kategori_ujian_id, guru_id, siswa_id, ujian_id
5. Setup model relationships
6. Run migrations

Output: Berikan saya daftar migration files untuk exam system yang sudah dibuat.
```

### Sub-Prompt 1.3: Create Models & Migrations for File Uploads & Settings

```
Create models untuk file uploads dan settings:

Models & Migrations:
1. LogoIdentitas (logo per tenant)
   - id, tenant_id, nama_file, path, file_type (enum: favicon/navbar_logo/etc), mime_type, size, uploaded_by (user_id), uploaded_at, updated_at
   - Relationships: belongsTo(User)

2. FileUpload (generic file uploads)
   - id, tenant_id, file_name, file_path, file_type, mime_type, size, uploadable_type (polymorphic), uploadable_id, uploaded_by, uploaded_at
   - Polymorphic relationship untuk bisa attach ke Guru, Siswa, Soal, etc

3. BeritaCache (education news cache)
   - id, tenant_id, title, description, source, url, image_url, published_at, cached_at, expires_at, created_at
   - Index: tenant_id, expires_at

Tasks:
1. Generate models & migrations
2. Setup migrations dengan schema di atas
3. Add soft deletes untuk LogoIdentitas
4. Setup polymorphic relationships di FileUpload
5. Run migrations

Output: Berikan saya migration files yang sudah dibuat.
```

### Sub-Prompt 1.4: Add Tenant Scoping to All Models

```
Setup tenant scoping otomatis di semua models:

Tasks:
1. Buat trait file: app/Traits/BelongsToTenant.php
   - Implement: public function tenant() relationship
   - Implement: boot() method dengan static::addGlobalScope(TenantScope::class)
   - Implement: static function for creating with tenant

2. Add trait ke semua tenant-scoped models:
   - Guru, Siswa, Soal, Ujian, JawabanSiswa, Nilai, KategoriUjian, LogoIdentitas, BeritaCache, FileUpload

3. Verify di models:
   - Pastikan query otomatis filtered by current tenant
   - Test dengan tinker: php artisan tinker → Siswa::all() (harus return empty atau hanya data tenant aktif)

Output: Berikan saya kode trait BelongsToTenant.php yang sudah dibuat.
```

---

# 📍 PHASE 2: Authentication & Authorization

## ⏱️ Estimasi: 1 jam | Kompleksitas: High | Priority: CRITICAL

Implementasi authentication untuk 4 role berbeda dengan 2 metode login berbeda.

### Sub-Prompt 2.1: Setup Google OAuth for Guru Login

```
Configure Google OAuth menggunakan Laravel Socialite:

Tasks:
1. Setup Google OAuth credentials:
   - Go to https://console.cloud.google.com
   - Create new project
   - Enable Google+ API
   - Create OAuth 2.0 credentials (Web Application)
   - Authorized redirect URIs: http://ujianku.test/auth/google/callback
   - Copy Client ID & Client Secret

2. Update .env:
   - GOOGLE_CLIENT_ID=...
   - GOOGLE_CLIENT_SECRET=...
   - GOOGLE_REDIRECT_URI=http://ujianku.test/auth/google/callback

3. Create Guru model if not exists
   - Add google_id column (nullable, unique)

4. Create GoogleCallback controller: app/Http/Controllers/Auth/GoogleCallbackController.php
   - handleCallback() method
   - Check if guru dengan email tersebut sudah ada
   - Jika belum ada: create guru baru
   - Jika sudah ada: update google_id
   - Redirect ke /guru/dashboard

5. Create routes di routes/web.php:
   - Route::get('/auth/google', [GoogleCallbackController::class, 'redirect'])->name('google.redirect');
   - Route::get('/auth/google/callback', [GoogleCallbackController::class, 'handleCallback'])->name('google.callback');

6. Update login view: resources/views/auth/login.blade.php
   - Add Google OAuth button
   - Button text: "Login dengan Google (Guru)"

Output: Berikan saya kode GoogleCallbackController yang sudah dibuat.
```

### Sub-Prompt 2.2: Setup NIS + Password Login for Siswa

```
Setup custom authentication untuk siswa menggunakan NIS + Password:

Tasks:
1. Create SiswaLoginRequest: app/Http/Requests/Auth/SiswaLoginRequest.php
   - Validate nis (required, exists in siswa table)
   - Validate password (required, min 6)
   - Custom message

2. Create SiswaAuthController: app/Http/Controllers/Auth/SiswaAuthController.php
   - login() method:
     - Cek NIS exists di Siswa model
     - Verify password dengan Hash::check()
     - Create session
     - Redirect ke /siswa/dashboard
   - logout() method:
     - Destroy session
     - Redirect ke login

3. Create Siswa login view: resources/views/auth/siswa-login.blade.php
   - Form input: NIS (text)
   - Form input: Password (password)
   - Submit button
   - Link ke guru login

4. Add middleware: app/Http/Middleware/IsSiswa.php
   - Check if authenticated user is siswa
   - If not, redirect ke login

5. Setup routes:
   - Route::get('/siswa/login', [SiswaAuthController::class, 'showLoginForm'])->name('siswa.login');
   - Route::post('/siswa/login', [SiswaAuthController::class, 'login'])->name('siswa.login.store');
   - Route::post('/siswa/logout', [SiswaAuthController::class, 'logout'])->name('siswa.logout');

Output: Berikan saya kode SiswaAuthController yang sudah dibuat.
```

### Sub-Prompt 2.3: Setup Admin & Super Admin Login

```
Setup login untuk Admin dan Super Admin menggunakan email + password:

Tasks:
1. Update User migration:
   - Add column: remember_token (nullable)
   - This is standard Laravel auth

2. Create AdminLoginRequest: app/Http/Requests/Auth/AdminLoginRequest.php
   - Validate email (required, email)
   - Validate password (required, min 6)

3. Create AdminAuthController: app/Http/Controllers/Auth/AdminAuthController.php
   - showLoginForm() method
   - login() method:
     - Use Auth::attempt(['email' => email, 'password' => password])
     - If authenticated:
       - Check role: super_admin or admin
       - Redirect ke appropriate dashboard
     - If not, redirect kembali ke login dengan error
   - logout() method

4. Create login views:
   - resources/views/auth/admin-login.blade.php
   - Form: email, password
   - Buttons: "Login Super Admin" atau "Login Admin"

5. Setup routes:
   - Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
   - Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');
   - Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

6. Create middleware: app/Http/Middleware/IsAdmin.php & IsAdminOrSuperAdmin.php

Output: Berikan saya kode AdminAuthController yang sudah dibuat.
```

### Sub-Prompt 2.4: Create Role-Based Middleware & Authorization

```
Create middleware untuk role-based access control:

Tasks:
1. Create middleware: app/Http/Middleware/CheckRole.php
   - Parameter: roles yang diizinkan (comma-separated)
   - Check if current user memiliki salah satu role
   - Jika tidak: abort(403) unauthorized

2. Create middleware: app/Http/Middleware/CheckTenant.php
   - Verify current tenant context
   - Set tenant dalam request context
   - Ensure all queries scoped to tenant

3. Register middleware di app/Http/Kernel.php:
   - Add CheckRole ke routeMiddleware
   - Add CheckTenant ke routeMiddleware

4. Create route groups dengan middleware:
   - Route::group(['middleware' => ['auth', 'checkTenant', 'checkRole:admin']], function () {
       Route::resource('guru', GuruController::class);
     });
   - Similarly untuk super_admin, guru, siswa routes

5. Update User model:
   - Add helper method: isSuperAdmin(), isAdmin(), isGuru(), isSiswa()

Output: Berikan saya kode CheckRole middleware yang sudah dibuat.
```

---

**TO BE CONTINUED in Part 2: Phase 3-7**

---

**Catatan Penting untuk Developer:**
- Jangan skip steps — setiap phase depend pada phase sebelumnya
- Test setiap step dengan: php artisan tinker, database inspection
- Jika ada error: READ pesan error dengan teliti, bukan langsung copy-paste solusi
- **MANUAL: Setelah phase sukses, lakukan commit ke Git:**
  ```
  git add .
  git commit -m "Phase X: [deskripsi]"
  ```
  (AI Agent HANYA siapkan kode, USER yang COMMIT)