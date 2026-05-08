# 🤖 UJIANKU-CBT — Agent Memory & Context File

**Dibuat:** 2026-05-08  
**Diperbarui:** 2026-05-08  
**Tujuan:** Memudahkan AI Agent (GitHub Copilot) memahami konteks project secara cepat tanpa harus membaca ulang SKILL.md setiap saat.

---

## 📌 TL;DR — Ringkasan Project

Platform **Computer-Based Test (CBT) multi-tenant** untuk Madrasah Tsanawiyah (MTs).  
Dibangun dengan **Laravel 11**, **PostgreSQL 18**, **Tailwind CSS + DaisyUI**, dan **stancl/tenancy v3**.  
Target: Produk SaaS pendidikan yang bisa digunakan banyak sekolah sekaligus.

---

## 🛠️ Tech Stack Wajib

| Layer      | Teknologi                          |
|------------|------------------------------------|
| Backend    | Laravel 11, PHP 8.5.5              |
| Database   | PostgreSQL 18 (port 5432)          |
| Web Server | Nginx 1.22.1                       |
| Frontend   | Tailwind CSS v3.4+, DaisyUI v4.0+  |
| Build Tool | Vite (bawaan Laravel 11)           |

### Package Mandatory (jangan diganti/dihapus)
```
stancl/tenancy v3.*          → Multi-tenancy
spatie/laravel-permission v6.* → Roles & Permissions
laravel/socialite v5.*       → Google OAuth (login Guru)
maatwebsite/excel v3.1.*     → Import soal dari Excel
laravel/breeze v2.*          → Auth scaffolding
intervention/image v3.*      → Resize/process gambar
```

---

## 🏗️ Arsitektur Kritis (Wajib Dipahami)

### Multi-Tenancy Strategy
- **Method:** Single database + `tenant_id` di setiap tabel tenant-scoped
- **Identifikasi:** Domain-based (`sekolah1.ujianku.test` → Tenant 1)
- **Library:** stancl/tenancy v3 dengan middleware untuk auto-scope query

### Tabel yang TIDAK perlu `tenant_id` (global/master):
- `users` (hanya super_admin & admin)
- `tenants`
- `roles`, `permissions` (spatie/laravel-permission)

### Tabel yang HARUS punya `tenant_id`:
- `guru`, `siswa`, `soal`, `kategori_ujian`, `ujian`
- `jawaban_siswa`, `nilai`
- `logo_identitas`, `file_uploads`, `berita_cache`

---

## 👥 Roles & Authentication

| Role        | Level    | Login Method              | Sesi     |
|-------------|----------|---------------------------|----------|
| super_admin | Platform | Email + Password          | 8 jam    |
| admin       | Tenant   | Email + Password          | 8 jam    |
| guru        | Tenant   | **Google OAuth**          | 8 jam    |
| siswa       | Tenant   | NIS + Password (default=NIS) | 2 jam |

### Hierarki Permission
```
super_admin  → manage tenants, global settings, platform analytics
  admin      → manage guru/siswa, approve exam, manage categories
    guru     → create soal/ujian, grade essay, view nilai siswa mereka
      siswa  → take exam, view history (TIDAK bisa lihat nilai langsung)
```

---

## 📁 Struktur Folder Penting

```
app/
├── Models/           → Tenant.php, User.php, Guru.php, Siswa.php, Soal.php,
│                       KategoriUjian.php, Ujian.php, JawabanSiswa.php,
│                       Nilai.php, LogoIdentitas.php, BeritaCache.php
├── Http/
│   ├── Controllers/
│   │   ├── SuperAdmin/   → TenantController, DashboardController
│   │   ├── Admin/        → GuruController, SiswaController, LogoController, DashboardController
│   │   ├── Guru/         → SoalController, UjianController, NilaiController, DashboardController
│   │   └── Siswa/        → ExamController, DashboardController
│   └── Middleware/
│       ├── CheckTenant.php
│       ├── EnsureTenantScope.php
│       └── EnsureRole.php
├── Services/         → ExamService, GradingService, AntiCheatService, NewsService, FileUploadService
├── Traits/           → BelongsToTenant.php (auto-scope semua models ke tenant aktif)
└── Jobs/             → ImportQuestionsFromExcel, FetchEducationNews, GenerateExamStatistics

routes/
├── web.php           → main routes
├── superadmin.php    → prefix: /super-admin
├── admin.php         → prefix: /admin
├── guru.php          → prefix: /guru
└── siswa.php         → prefix: /siswa

resources/views/
├── layouts/          → app.blade.php, auth.blade.php, minimal.blade.php
├── components/       → navbar.blade.php, sidebar.blade.php, stats-card.blade.php
├── superadmin/, admin/, guru/, siswa/
```

---

## 🗄️ Schema Model Utama

### User (super_admin & admin)
`id | tenant_id (nullable) | name | email | password | role | foto_profil | is_active`

### Guru
`id | tenant_id | user_id (nullable) | email | nama | nip | foto_profil | is_wali_kelas | is_active`

### Siswa
`id | tenant_id | nis (unique per tenant) | nama | email (nullable) | password | foto | kelas | is_active`

### KategoriUjian
`id | tenant_id | nama | deskripsi | urutan | is_active`

### Soal
`id | tenant_id | kategori_ujian_id | guru_id | pertanyaan | tipe_soal (pilihan_ganda/essay) | opsi_a/b/c/d | kunci_jawaban | bobot | is_active`

### Ujian
`id | tenant_id | guru_id | kategori_ujian_id | judul | deskripsi | tgl_mulai | tgl_selesai | waktu_durasi | is_acak_soal | is_acak_opsi | is_active`

### JawabanSiswa
`id | tenant_id | ujian_id | siswa_id | soal_id | jawaban | waktu_mulai | waktu_selesai | is_submitted`

### Nilai
`id | tenant_id | ujian_id | siswa_id | nilai_otomatis | nilai_essay | nilai_akhir | status (lulus/tidak_lulus) | catatan_guru`

### LogoIdentitas
`id | tenant_id | nama_file | path | file_type (favicon/navbar_logo) | mime_type | size | uploaded_by | uploaded_at`

### BeritaCache
`id | tenant_id | title | description | source | url | image_url | published_at | cached_at | expires_at`

---

## 🎨 Design System

### Warna
```
Primary   : #3B82F6 (Blue)
Secondary : #10B981 (Green)
Danger    : #EF4444 (Red)
Warning   : #F59E0B (Amber)
Dark      : #1F2937
Light     : #F9FAFB
```

### Komponen DaisyUI Standar
```
Button    : btn btn-primary / btn-secondary / btn-danger
Form      : input input-bordered, textarea textarea-bordered
Card      : card bg-base-100 shadow
Table     : table table-compact table-hover
Alert     : alert alert-info / alert-success / alert-error
```

### Layout
- Sidebar untuk admin/guru (collapsible di mobile)
- Navbar konsisten di semua halaman
- Mobile-first (Tailwind responsive: sm: md: lg: xl:)
- Max-width: 7xl, Padding: 1rem (mobile) / 2rem (desktop)

---

## 🔒 Security Rules Wajib

### Selalu Lakukan
- CSRF protection semua form
- Bcrypt hash untuk password
- Middleware role check (`EnsureRole.php`)
- Tenant isolation via `EnsureTenantScope.php` + `BelongsToTenant` trait
- File uploads: validasi size + extension, simpan di `/storage/app` bukan `/public`
- Sanitasi input via Laravel's `$fillable`

### Anti-Cheat Saat Ujian (siswa)
- Disable copy-paste & right-click
- Deteksi keluar fullscreen (JS: `fullscreenchange` event)
- Deteksi pindah tab (`visibilitychange` event)
- Randomize urutan soal & opsi jawaban
- Single session per browser (`localStorage` token check)
- Timer countdown keseluruhan + per soal
- Log semua aktivitas ujian (timestamp, durasi per soal)

---

## 📰 Integrasi Berita Pendidikan

- **Sumber:** Google News RSS
- **Refresh:** Setiap 1 jam (via Job: `FetchEducationNews`)
- **Keywords:** "pendidikan", "kurikulum", "MTs", "ujian"
- **Cache:** Disimpan di tabel `berita_cache` dengan `expires_at`

---

## 🚦 Naming Conventions Wajib

| Konteks       | Convention          | Contoh                           |
|---------------|---------------------|----------------------------------|
| DB Tables     | snake_case, plural  | `soal`, `kategori_ujian`         |
| DB Columns    | snake_case          | `created_at`, `tenant_id`        |
| FK            | `{singular}_id`     | `guru_id`, `ujian_id`            |
| Models        | PascalCase singular | `Soal`, `KategoriUjian`          |
| Controllers   | PascalCase + "Controller" | `SoalController`          |
| Views         | kebab-case.blade.php | `soal-list.blade.php`           |
| Routes (URL)  | kebab-case          | `/guru/soal-list`                |
| Relationships | camelCase           | `guruSoal()`, `ujianSiswa()`     |
| Scopes        | camelCase           | `scopeActive()`, `scopeByTenant()`|

---

## 📍 Status Phases Development

| Phase | Nama                            | Estimasi | Status       |
|-------|---------------------------------|----------|--------------|
| 0     | Project Setup & Initial Config  | 30 mnt   | ⬜ Belum mulai |
| 1     | Database Design & Migrations    | 45 mnt   | ⬜ Belum mulai |
| 2     | Authentication & Authorization  | 1 jam    | ⬜ Belum mulai |
| 3     | User Management & Profile       | 45 mnt   | ⬜ Belum mulai |
| 4     | Exam Engine & Question Mgmt     | 2 jam    | ⬜ Belum mulai |
| 5     | Grading System                  | —        | ⬜ Belum mulai |
| 6     | Dashboard & Analytics           | —        | ⬜ Belum mulai |
| 7     | Deployment & Finalisasi         | —        | ⬜ Belum mulai |

> Update status ini setiap kali sebuah phase selesai dikerjakan.

---

## 📚 Referensi Cepat

- **Context lengkap:** `.agents/skills/ujianku-cbt/SKILL.md`
- **Prompts Phase 0–2:** `.agents/skills/ujianku-cbt/PROMPTS-Part-1.md`
- **Prompts Phase 3–7:** `.agents/skills/ujianku-cbt/PROMPTS-Part-2.md`
- **Panduan Developer:** `.agents/skills/ujianku-cbt/QUICK-START-GUIDE.md`
- **stancl/tenancy docs:** https://tenancyforlaravel.com/
- **Spatie Permission docs:** https://spatie.be/docs/laravel-permission/
- **DaisyUI components:** https://daisyui.com/

---

## 🗒️ Catatan Agent

- Proyek ini masih kosong — **belum ada Laravel install** di workspace saat ini
- Workspace hanya berisi: `.agents/`, `.git/`, `.github/`, `.gitignore`
- **Langkah pertama:** Install Laravel 11 fresh di folder `/workdir/www/ujianku-cbt`
- Developer adalah **junior developer** — gunakan komentar kode bahasa Indonesia, jelas, dan terbaca
- Selalu tanya jika ada ambiguitas sebelum generate kode
