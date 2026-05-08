---
name: ujianku-cbt
description: Describe what this skill does and when to use it. Include keywords that help agents identify relevant tasks.
---

<!-- Tip: Use /create-skill in chat to generate content with agent assistance -->

# 🎓 UJIANKU-CBT — AI Agent Context File (SKILL.md)

**Versi:** 1.0  
**Tanggal:** 2026-05-08  
**Status:** Active Development  
**AI Agent:** GitHub Copilot Enterprise

---

## 📋 Project Overview

**Nama Proyek:** UJIANKU-CBT  
**Deskripsi:** Platform Computer-Based Test (CBT) multi-tenant modern untuk Madrasah Tsanawiyah (MTs) dengan desain minimalis dan fitur anti-cheat optimal.

**Visi Jangka Panjang:** Menjadi produk SaaS pendidikan yang dapat diakses oleh multiple sekolah (multi-tenant) dengan skalabilitas tinggi.

---

## 🛠️ Tech Stack (WAJIB DIIKUTI)

### Backend
```
Framework       : Laravel 11 (latest stable)
PHP Version     : 8.5.5
Web Server      : Nginx 1.22.1
Package Manager : Composer (latest)
```

### Database
```
DBMS            : PostgreSQL 18
Connection      : PDO PostgreSQL
Port            : 5432
Charset         : UTF-8
```

### Frontend
```
CSS Framework   : Tailwind CSS v3.4+
Component Lib   : DaisyUI v4.0+
JS Runtime      : Node.js (latest LTS)
Package Manager : NPM (latest)
Build Tool      : Vite (bawaan Laravel 11)
```

### Key Packages (MANDATORY)
```
- stancl/tenancy v3.* (Multi-tenancy)
- spatie/laravel-permission v6.* (Roles & Permissions)
- laravel/socialite v5.* (Google OAuth)
- maatwebsite/excel v3.1.* (Excel import)
- laravel/breeze v2.* (Auth scaffolding)
- intervention/image v3.* (Image processing)
```

### Development Tools
```
IDE             : VS Code
AI Assistant    : GitHub Copilot Enterprise
Version Control : Git
Environment     : .env file (JANGAN commit ke repo)
```

---

## 🏗️ Architecture Decisions (KRITIS)

### Multi-Tenant Strategy
**Method:** Database-per-host dengan stancl/tenancy v3  
**Reasoning:** 
- Scalability tinggi untuk produk SaaS
- Data isolation terjamin per tenant (sekolah)
- Simple upgrade path untuk junior developer
- Documented well dengan community besar

**Tenant Identification:**
```
Domain-based routing:
- sekolah1.ujianku.test → Tenant 1
- sekolah2.ujianku.test → Tenant 2
- app.ujianku.test → Super Admin (platform level)

Alternative (Subdomain-less):
- ujianku.test/sekolah1 → Tenant 1
```

### Database Schema Strategy
**Approach:** Single database dengan `tenant_id` pada setiap tabel  
**Tenancy Scoping:** Automatic dengan stancl/tenancy middleware  

**Master Tables (NOT scoped to tenant):**
```
- users (super_admin role)
- tenants
- roles (platform-wide)
- permissions (platform-wide)
```

**Tenant-scoped Tables:**
```
- guru
- siswa
- soal
- kategori_ujian
- ujian
- jawaban_siswa
- nilai
- logo_identitas
- file_uploads
- berita_cache
- dll
```

### Authentication Strategy

**Super Admin & Admin:**
```
Method    : Email + Password (hashed bcrypt)
Storage   : users table
Roles     : super_admin, admin
Session   : Laravel session (cookie-based)
```

**Guru:**
```
Method    : Google OAuth (Socialite)
Provider  : Google
Redirect  : /auth/google/callback
Roles     : guru, wali_kelas (optional dual role)
Session   : Laravel session
```

**Siswa:**
```
Method    : NIS (Nomor Induk Siswa) + Password
Storage   : siswa table (jangan di users table)
Rules     : Password = NIS (default), admin/guru bisa change
Session   : Laravel session dengan timeout 2 jam (exam duration)
```

### Roles & Permissions Hierarchy

```
LEVEL 1: SUPER ADMIN (Platform)
  ├── Create/Delete/Update tenants
  ├── Manage global settings
  ├── View platform-wide analytics
  └── Upload logo per tenant

LEVEL 2: ADMIN (Per Tenant/Sekolah)
  ├── Manage guru & siswa
  ├── Approve exam schedules
  ├── Manage exam categories
  ├── Nonaktifkan/delete akun
  └── Upload foto siswa

LEVEL 3: GURU (Per Tenant)
  ├── Create questions (pilihan ganda & essay)
  ├── Import soal dari Excel
  ├── Create & schedule exam
  ├── Grade essay questions
  ├── View hasil siswa mereka
  └── Wali kelas: entry nama+NIS siswa

LEVEL 4: SISWA (Per Tenant)
  ├── Take exam (soal pilihan ganda otomatis, essay manual)
  ├── View history
  └── (NO access to grades — only guru/admin)
```

---

## 📁 Project Structure (WAJIB)

```
ujianku-cbt/
├── .agents/
│   └── skills/
│       └── ujianku-cbt/
│           ├── SKILL.md (← File ini)
│           ├── PROMPTS.md
│           └── ARCHITECTURE.md
│
├── app/
│   ├── Models/
│   │   ├── Tenant.php
│   │   ├── User.php (super_admin, admin)
│   │   ├── Guru.php
│   │   ├── Siswa.php
│   │   ├── Soal.php
│   │   ├── KategoriUjian.php
│   │   ├── Ujian.php
│   │   ├── JawabanSiswa.php
│   │   ├── Nilai.php
│   │   ├── LogoIdentitas.php
│   │   └── BeritaCache.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SuperAdmin/
│   │   │   │   ├── TenantController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Admin/
│   │   │   │   ├── GuruController.php
│   │   │   │   ├── SiswaController.php
│   │   │   │   ├── LogoController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Guru/
│   │   │   │   ├── SoalController.php
│   │   │   │   ├── UjianController.php
│   │   │   │   ├── NilaiController.php
│   │   │   │   └── DashboardController.php
│   │   │   └── Siswa/
│   │   │       ├── ExamController.php
│   │   │       └── DashboardController.php
│   │   │
│   │   └── Middleware/
│   │       ├── CheckTenant.php
│   │       ├── EnsureTenantScope.php
│   │       └── EnsureRole.php
│   │
│   ├── Services/
│   │   ├── ExamService.php
│   │   ├── GradingService.php
│   │   ├── AntiCheatService.php
│   │   ├── NewsService.php
│   │   └── FileUploadService.php
│   │
│   └── Jobs/
│       ├── ImportQuestionsFromExcel.php
│       ├── FetchEducationNews.php
│       └── GenerateExamStatistics.php
│
├── database/
│   ├── migrations/
│   │   ├── 2024_*_create_tenants_table.php
│   │   ├── 2024_*_create_users_table.php
│   │   ├── 2024_*_create_guru_table.php
│   │   ├── 2024_*_create_siswa_table.php
│   │   ├── 2024_*_create_soal_table.php
│   │   ├── 2024_*_create_kategori_ujian_table.php
│   │   ├── 2024_*_create_ujian_table.php
│   │   ├── 2024_*_create_jawaban_siswa_table.php
│   │   ├── 2024_*_create_nilai_table.php
│   │   ├── 2024_*_create_logo_identitas_table.php
│   │   ├── 2024_*_create_berita_cache_table.php
│   │   └── 2024_*_create_role_permission_tables.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleAndPermissionSeeder.php
│       └── SuperAdminSeeder.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php (base layout dengan navbar konsisten)
│   │   │   ├── auth.blade.php
│   │   │   └── minimal.blade.php
│   │   ├── superadmin/
│   │   ├── admin/
│   │   ├── guru/
│   │   ├── siswa/
│   │   └── components/
│   │       ├── navbar.blade.php
│   │       ├── sidebar.blade.php
│   │       └── stats-card.blade.php
│   │
│   ├── css/
│   │   ├── app.css (Tailwind imports)
│   │   └── custom.css (Custom styles)
│   │
│   └── js/
│       ├── app.js
│       ├── anti-cheat.js (fullscreen, tab switch detection)
│       └── exam-timer.js
│
├── routes/
│   ├── web.php (main routes)
│   ├── api.php (optional untuk future mobile app)
│   ├── superadmin.php (prefix: /super-admin)
│   ├── admin.php (prefix: /admin)
│   ├── guru.php (prefix: /guru)
│   └── siswa.php (prefix: /siswa)
│
├── config/
│   ├── tenancy.php (stancl/tenancy config)
│   ├── permission.php (spatie/permission config)
│   └── database.php (multi-db connection)
│
├── storage/
│   └── app/
│       ├── tenants/ (per-tenant file storage)
│       ├── logos/
│       ├── foto_profil/
│       └── foto_siswa/
│
├── .env.example (JANGAN include credentials real!)
├── .env (← JANGAN COMMIT KE GIT)
├── docker-compose.yml (optional, untuk dev environment)
├── package.json (Tailwind, DaisyUI config)
└── tailwind.config.js (DaisyUI theming)
```

---

## 🎨 Design System (UI/UX Standards)

### Color Palette
```
Primary     : #3B82F6 (Blue)
Secondary   : #10B981 (Green)
Danger      : #EF4444 (Red)
Warning     : #F59E0B (Amber)
Success     : #10B981 (Green)
Dark        : #1F2937 (Dark Gray)
Light       : #F9FAFB (Light Gray)
```

### Typography
```
Font Family : Inter, system-ui, sans-serif (default)
H1          : text-4xl font-bold
H2          : text-3xl font-bold
H3          : text-2xl font-semibold
Body        : text-base font-normal
Small       : text-sm font-normal
```

### Layout Rules
```
- Sidebar for admin/guru (collapsible on mobile)
- Top navbar pada semua halaman (KONSISTEN!)
- Mobile-first responsive design
- Max-width 7xl untuk main container
- Padding konsisten: 1rem (mobile), 2rem (desktop)
```

### Component Standards
```
Buttons     : btn btn-primary, btn btn-secondary, etc. (DaisyUI)
Forms       : input input-bordered, textarea textarea-bordered
Cards       : card bg-base-100 shadow
Tables      : table table-compact table-hover
Alerts      : alert alert-info, alert alert-success, etc.
```

---

## 📊 Database Models (Outline)

### User Model (Super Admin & Admin)
```
- id
- tenant_id (nullable — super admin doesn't have tenant)
- name
- email
- password (hashed)
- role (super_admin / admin)
- foto_profil (path ke file)
- is_active
- created_at, updated_at
```

### Guru Model
```
- id
- tenant_id
- user_id (reference ke User jika ada, bisa null)
- email (google email)
- nama
- nip (nomor identitas pegawai)
- foto_profil
- is_wali_kelas
- is_active
- created_at, updated_at
```

### Siswa Model
```
- id
- tenant_id
- nis (nomor induk siswa — unique per tenant)
- nama
- email (optional)
- password (hashed — default: hashed NIS)
- foto (di-upload admin/wali kelas)
- kelas
- created_at, updated_at
```

### KategoriUjian Model
```
- id
- tenant_id
- nama (ASTS, Formatif, Sumatif Harian, dll)
- deskripsi
- urutan
- created_at, updated_at
```

### Soal Model
```
- id
- tenant_id
- kategori_ujian_id
- guru_id
- pertanyaan
- tipe_soal (pilihan_ganda, essay)
- opsi_a, opsi_b, opsi_c, opsi_d (untuk pilihan ganda)
- kunci_jawaban (untuk pilihan ganda = A/B/C/D; essay = expected answer)
- bobot (score)
- is_active
- created_at, updated_at
```

### Ujian Model
```
- id
- tenant_id
- guru_id
- kategori_ujian_id
- judul
- deskripsi
- tgl_mulai
- tgl_selesai
- waktu_durasi (menit)
- is_acak_soal
- is_acak_opsi
- is_active
- created_at, updated_at
```

### JawabanSiswa Model
```
- id
- tenant_id
- ujian_id
- siswa_id
- soal_id
- jawaban (jawaban pilihan ganda atau essay text)
- waktu_mulai
- waktu_selesai
- is_submitted
- created_at, updated_at
```

### Nilai Model
```
- id
- tenant_id
- ujian_id
- siswa_id
- nilai_otomatis (untuk pilihan ganda)
- nilai_essay (di-input guru)
- nilai_akhir (otomatis: (otomatis + essay) / 2)
- status (lulus, tidak_lulus)
- catatan_guru
- created_at, updated_at
```

---

## 🔐 Security Rules (WAJIB!)

### Authentication
```
✓ Use Laravel's built-in bcrypt hashing
✓ Enable CSRF protection on all forms
✓ Use middleware untuk role checking
✓ Google OAuth validation on callback
✓ Session timeout 2 jam untuk siswa
✓ Session timeout 8 jam untuk guru/admin
```

### Data Protection
```
✓ Tenant isolation via middleware (EnsureTenantScope)
✓ Query scoping dengan ->whereTenantId()
✓ File uploads harus di /storage, bukan /public
✓ Sanitize input dengan Laravel's fillable/guarded
✓ Validate file uploads (size, extension)
✓ Use Laravel's security headers
```

### Mobile Security (Anti-Cheat)
```
✓ Disable copy-paste saat exam
✓ Disable right-click context menu
✓ Detect fullscreen exit (JavaScript)
✓ Detect tab switch (JavaScript)
✓ Randomize question order per exam instance
✓ Randomize answer options order
✓ Single session per browser (cek localStorage)
✓ Time limit per soal + overall exam
✓ Log all exam activities (timestamp, durasi per soal)
```

---

## 📱 Mobile Optimization Rules

```
✓ Mobile-first CSS (Tailwind responsive: sm: md: lg: xl:)
✓ Viewport meta tag set properly
✓ Touch-friendly buttons (min 44px height)
✓ Responsive navbar (hamburger menu on mobile)
✓ No horizontal scroll
✓ Optimize images (WebP, lazy loading)
✓ Minimize JS bundle (only what's needed per page)
✓ Test on real devices: iPhone 6, Android 5.0+
```

---

## 📰 News Integration

**Source:** Google News RSS + Cache DB  
**Refresh Interval:** 1 jam  
**Keywords:** "pendidikan", "kurikulum", "MTs", "ujian"  
**Cache Table:**
```
- id
- tenant_id
- title
- description
- source
- url
- published_at
- cached_at
- expires_at
```

**Queue Job:** FetchEducationNews (runs every hour)

---

## 📝 Naming Conventions (WAJIB!)

### Database
```
✓ Table names: snake_case, plural (soal, guru, siswa)
✓ Column names: snake_case (created_at, updated_at, tenant_id)
✓ Foreign keys: {table_singular}_id (guru_id, ujian_id)
✓ Indexes: idx_{table}_{column}
```

### Laravel Models
```
✓ Singular, PascalCase (Soal, Guru, Siswa, KategoriUjian)
✓ Relations: camelCase (guruSoal(), ujianSiswa())
✓ Scopes: camelCase (scopeActive(), scopeByTenant())
```

### Controllers
```
✓ PascalCase + "Controller" (SoalController, UjianController)
✓ Methods: camelCase (store(), update(), destroy())
✓ Nested: SuperAdmin\TenantController, Admin\SiswaController
```

### Views
```
✓ kebab-case.blade.php (soal-list.blade.php, ujian-form.blade.php)
✓ Component: _navbar.blade.php, _sidebar.blade.php
```

### Routes
```
✓ kebab-case untuk URL (/guru/soal-list, /siswa/ujian-aktif)
✓ Resource routes: Route::resource('soal', SoalController)
```

---

## 🚀 Deployment Checklist

Before going to production:
```
□ .env production configuration
□ Database migrations tested di production
□ All images optimized
□ Caching configured (Redis/Memcached)
□ SSL/HTTPS enabled
□ Nginx security headers configured
□ Backup strategy implemented
□ Monitoring/logging setup
□ Rate limiting enabled
□ GDPR compliance (if applicable)
```

---

## 📚 Key Documentation Links

- stancl/tenancy: https://tenancyforlaravel.com/
- Spatie Permission: https://spatie.be/docs/laravel-permission/
- Laravel Socialite: https://laravel.com/docs/socialite
- Tailwind CSS: https://tailwindcss.com/
- DaisyUI: https://daisyui.com/

---

## 👤 Developer Notes

**Junior Developer:** Okay, perlu guidance & code review regular  
**Sesuaikan pace:** Jangan terburu-buru, understand architecture dulu  
**Use AI Agent:** GitHub Copilot akan help generate boilerplate, tapi always review hasilnya  
**Testing:** Write tests seiring dengan development (TDD approach optimal)

---

**Last Updated:** 2026-05-08  
**Maintained By:** Konsultan IT Development  
**Status:** ACTIVE