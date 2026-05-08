# 🚀 UJIANKU-CBT — Master Prompt & Sub-Prompts (Part 2)

**Lanjutan dari PROMPTS-Part-1.md**  
**Phase 3-7**

---

## 📌 MASTER CONTEXT (BACA DULU!)

Sebelum menjalankan sub-prompts di Part 2, ensure kamu sudah aware:

```
⚠️ CRITICAL: GIT OPERATIONS
- ❌ JANGAN lakukan: git commit, git push, git add
- ❌ JANGAN jalankan git commands apapun
- ✅ HANYA USER yang boleh commit & push manual
- ✅ Kamu hanya siapkan kode yang siap commit
- ✅ Di akhir setiap phase, informkan: "Kode siap commit oleh user"

WORKFLOW:
1. Generate/update kode sesuai prompt
2. Test setiap fitur hingga working
3. Informkan phase selesai
4. USER yang MANUAL commit ke Git
5. Lanjut ke phase berikutnya
```

---

# 📍 PHASE 3: User Management & Profile System

## ⏱️ Estimasi: 45 menit | Kompleksitas: Medium | Priority: HIGH

### Sub-Prompt 3.1: File Upload Service for Photos

```
Create service untuk handling file uploads (foto profil, foto siswa, logo):

Tasks:
1. Create service: app/Services/FileUploadService.php
   - Method: uploadProfilePhoto(file, user_type, user_id)
     - Validate: jpg, jpeg, png, max 2MB
     - Resize: 300x300 pixels
     - Store di: storage/app/tenants/{tenant_id}/profile_photos/{user_type}/{user_id}.jpg
     - Return: relative path untuk save ke DB
   
   - Method: uploadStudentPhoto(file, siswa_id)
     - Validate: jpg, jpeg, png, max 2MB
     - Resize: 200x200 pixels
     - Store di: storage/app/tenants/{tenant_id}/student_photos/{siswa_id}.jpg
     - Return: relative path
   
   - Method: uploadLogo(file, tenant_id, logo_type)
     - Validate: jpg, jpeg, png, svg, max 1MB
     - Jika favicon: resize 16x16, 32x32
     - Jika navbar_logo: resize 200x50
     - Store di: storage/app/tenants/{tenant_id}/logos/{logo_type}.{ext}
     - Return: relative path
   
   - Method: deleteFile(file_path)
     - Delete file dari storage
   
   - Use: Intervention\Image untuk resize

2. Create FileUploadRequest validation: app/Http/Requests/FileUploadRequest.php
   - Validate file size, extension, mime type
   - Use Laravel's validation rules

3. Create test case:
   - Test upload valid file → success
   - Test upload invalid file → validation error
   - Test file deletion → file removed

Output: Berikan saya kode FileUploadService.php yang sudah dibuat.
```

### Sub-Prompt 3.2: Create Controllers for Admin User Management

```
Create controllers untuk admin manage guru dan siswa:

Controllers to create:
1. Admin/GuruController.php
   - index() → list semua guru di tenant ini
   - create() → form create guru baru
   - store(GuruRequest) → save guru baru
     - Generate NIP otomatis jika kosong
     - Set password = NIP (hashed)
   - edit(id) → edit form
   - update(GuruRequest, id) → update guru
   - destroy(id) → soft delete guru
   - show(id) → detail guru
   - Methods: uploadProfilePhoto(File)

2. Admin/SiswaController.php
   - index() → list semua siswa
   - create() → form input nama + NIS
   - store(SiswaRequest) → save siswa baru
     - Generate password = NIS
     - Activate user otomatis
   - edit(id) → edit form
   - update(SiswaRequest, id) → update data siswa
   - destroy(id) → soft delete siswa
   - show(id) → detail siswa
   - uploadPhoto(id, file) → upload foto siswa
   - deactivate(id) → nonaktifkan akun siswa
   - activate(id) → aktivkan akun siswa

3. Guru/SiswaManagementController.php (Khusus Wali Kelas)
   - index() → list siswa kelas yg diajar
   - create() → form add siswa (bulk entry NIS)
   - store(BulkSiswaRequest) → create multiple siswa sekaligus
   - uploadStudentPhoto(id, file) → wali kelas upload foto siswa

Tasks:
1. Generate controllers dengan: php artisan make:controller [name]
2. Setup request validation classes
3. Implement semua methods dengan proper error handling
4. Add authorization checks: policy atau middleware
5. Add logging untuk audit trail (optional)

Output: Berikan saya kode GuruController.php yang sudah dibuat.
```

### Sub-Prompt 3.3: Create Views for User Management

```
Create Blade views untuk user management:

Views to create:
1. Admin Dashboard:
   - resources/views/admin/dashboard.blade.php
   - Show: total guru, total siswa, upcoming exams, latest activities
   - Quick links ke guru list, siswa list, exam list

2. Guru Management:
   - resources/views/admin/guru/index.blade.php (table with actions)
   - resources/views/admin/guru/create.blade.php (form)
   - resources/views/admin/guru/edit.blade.php (form)
   - resources/views/admin/guru/show.blade.php (detail)

3. Siswa Management:
   - resources/views/admin/siswa/index.blade.php (table with actions)
   - resources/views/admin/siswa/create.blade.php (form bulk entry)
   - resources/views/admin/siswa/edit.blade.php (form)
   - resources/views/admin/siswa/show.blade.php (detail + foto)

4. Profile Management:
   - resources/views/profile/edit.blade.php (untuk semua role)
   - resources/views/profile/upload-photo.blade.php (modal untuk upload foto)

Tasks:
1. Create semua views dengan Tailwind + DaisyUI components
2. Use form components: input, select, textarea (DaisyUI)
3. Add action buttons: edit, delete, upload, deactivate
4. Add confirmation dialogs untuk delete actions
5. Add success/error flash messages

Design principles:
- Mobile responsive (mobile-first)
- Consistent styling
- Clear CTAs (Call To Action)
- Form validation messages ditampilkan jelas

Output: Berikan saya kode view guru/index.blade.php yang sudah dibuat.
```

---

# 📍 PHASE 4: Exam Engine & Question Management

## ⏱️ Estimasi: 2 jam | Kompleksitas: High | Priority: CRITICAL

### Sub-Prompt 4.1: Exam Categories & Question Bank Management

```
Create functionality untuk manage exam categories dan question bank:

Controllers to create:
1. Guru/KategoriUjianController.php (READ ONLY — admin create)
   - index() → list kategori ujian (ASTS, Formatif, dll)
   - show(id) → detail kategori dengan jumlah soal

2. Admin/KategoriUjianController.php
   - index() → list kategori
   - create() → form create kategori baru
   - store(KategoriRequest) → save kategori
   - update(KategoriRequest, id) → edit kategori
   - destroy(id) → delete kategori

3. Guru/SoalController.php
   - index() → list soal milik guru ini (filtered by kategori)
   - create() → form create soal baru
   - store(SoalRequest) → save soal
     - Tipe soal: pilihan ganda atau essay
     - Untuk PG: opsi_a, opsi_b, opsi_c, opsi_d + kunci jawaban
     - Untuk essay: kunci_jawaban = expected answer (untuk reference)
   - edit(id) → edit soal
   - update(SoalRequest, id) → update soal
   - destroy(id) → delete soal (soft delete)
   - duplicate(id) → duplicate soal existing
   - search() → search soal by keyword

Tasks:
1. Generate controllers & requests
2. Implement input validation
3. Add authorization: guru hanya bisa manage soal sendiri
4. Add category filtering di question list
5. Add bulk actions: download as Excel, delete multiple, etc

Output: Berikan saya kode SoalController.php yang sudah dibuat.
```

### Sub-Prompt 4.2: Excel Import untuk Bulk Question Creation

```
Create functionality untuk import soal dari Excel:

Tasks:
1. Create Excel import class: app/Imports/SoalImport.php
   - Use Maatwebsite\Excel
   - Read rows dari Excel file
   - Expected columns: Pertanyaan, Tipe (PG/Essay), OpsiA, OpsiB, OpsiC, OpsiD, KunciJawaban, Bobot
   - Validation:
     - Tipe soal harus PG atau Essay
     - Soal tidak boleh duplikat
     - Jawaban tidak boleh kosong
   - Create soal records dalam database
   - Return: jumlah soal berhasil diimport, errors (jika ada)

2. Create Excel export class: app/Exports/SoalTemplate.php
   - Generate template Excel kosong dengan columns yang benar
   - Include contoh data (1-2 rows)

3. Create controller: Guru/SoalImportController.php
   - showForm() → display upload form + download template
   - import(Request) → process Excel file
     - Validate file type (xlsx, csv)
     - Run SoalImport
     - Return: success message + report

4. Create views:
   - resources/views/guru/soal/import.blade.php (upload form + template download)
   - resources/views/guru/soal/import-result.blade.php (hasil import)

5. Create routes:
   - Route::get('/guru/soal/import', [SoalImportController::class, 'showForm']);
   - Route::post('/guru/soal/import', [SoalImportController::class, 'import']);
   - Route::get('/guru/soal/template', [SoalImportController::class, 'downloadTemplate']);

Output: Berikan saya kode SoalImport.php yang sudah dibuat.
```

### Sub-Prompt 4.3: Ujian (Exam) Creation & Scheduling

```
Create functionality untuk guru create dan schedule ujian:

Controllers:
1. Guru/UjianController.php
   - index() → list exam yang dibuat guru ini
   - create() → form create exam baru
   - store(UjianRequest) → save exam
     - Fields: judul, deskripsi, kategori_ujian_id, tgl_mulai, tgl_selesai, waktu_durasi (menit)
     - Checkboxes: is_acak_soal (randomize questions), is_acak_opsi (randomize options)
     - Create default: is_active = true
   
   - edit(id) → edit exam (hanya jika belum dimulai)
   - update(UjianRequest, id) → update exam
   - destroy(id) → soft delete exam
   - show(id) → detail exam + list soal yang included
   
   - addQuestions(id) → form untuk add questions ke exam ini
   - assignQuestions(id, Request) → save assigned questions (many-to-many pivot table)
   - removeQuestion(id, soal_id) → remove question dari exam
   
   - activate(id) → aktifkan exam (set is_active = true)
   - deactivate(id) → nonaktifkan exam (set is_active = false, siswa tidak bisa akses)

2. Siswa/ExamController.php (READ ONLY)
   - index() → list exam yang available untuk siswa ini
   - show(id) → detail exam (before taking)

Tasks:
1. Create migration untuk exam_questions pivot table:
   - id, exam_id, soal_id, urutan (untuk randomize persistence), created_at
   - Indexes: exam_id, soal_id

2. Add relationship di Ujian model:
   - belongsToMany(Soal) melalui exam_questions pivot table

3. Create controller + request validation

4. Create views:
   - resources/views/guru/ujian/index.blade.php
   - resources/views/guru/ujian/create.blade.php
   - resources/views/guru/ujian/edit.blade.php
   - resources/views/guru/ujian/manage-questions.blade.php

Output: Berikan saya kode UjianController.php yang sudah dibuat.
```

### Sub-Prompt 4.4: Exam Taking Interface & Anti-Cheat System

```
Create interface untuk siswa take exam + anti-cheat:

Controllers:
1. Siswa/ExamController.php
   - startExam(id) → redirect ke exam interface dengan fullscreen
   - getQuestion(ujian_id, soal_index) → get soal berikutnya (via AJAX)
   - submitAnswer(ujian_id, soal_id, jawaban) → save answer (AJAX)
   - finishExam(ujian_id) → submit semua jawaban + calculate score
   - getTimeRemaining(ujian_id) → get sisa waktu (AJAX)

Tasks:
1. Create exam interface view: resources/views/siswa/exam/take.blade.php
   - Header: exam title, sisa waktu (update real-time), progress bar
   - Main area: soal (left), jawaban (right)
   - Navigation: previous, next, finish exam
   - Features: timer per soal, progress indicator
   - Mobile responsive

2. Create anti-cheat JavaScript: resources/js/anti-cheat.js
   - Fullscreen detection:
     - On exam start: requestFullscreen()
     - On fullscreen exit: detect & warning
     - On warning ignore: force exit exam (auto-submit)
   
   - Tab/window switch detection:
     - Use: document.hidden & visibilitychange event
     - On switch: pause timer, show warning
     - If switch >2 times: force exit
   
   - Copy-paste disable:
     - document.addEventListener('copy', e => e.preventDefault())
     - document.addEventListener('cut', e => e.preventDefault())
   
   - Right-click disable:
     - document.addEventListener('contextmenu', e => e.preventDefault())
   
   - Time sync:
     - AJAX call setiap 10 detik untuk verify time di server
     - Jika timeout exceed: auto-submit
   
   - Session detection:
     - localStorage: exam_session_id
     - Prevent multiple window dari exam yang sama
     - Jika detect: block & show error

3. Create middleware: app/Http/Middleware/VerifyExamSession.php
   - Check if siswa login & have active exam
   - Check if exam belum finish
   - Check exam time validity

4. Create notification:
   - Browser notification saat exam mau finish (5 menit countdown)

5. Create API endpoints (untuk AJAX):
   - GET /api/ujian/{id}/soal/{index}
   - POST /api/ujian/{id}/answer
   - GET /api/ujian/{id}/time-remaining
   - POST /api/ujian/{id}/finish

Output: Berikan saya kode anti-cheat.js yang sudah dibuat.
```

### Sub-Prompt 4.5: Automatic Grading untuk Pilihan Ganda & Manual Essay Grading

```
Create service untuk grading otomatis (PG) dan manual (Essay):

Services to create:
1. app/Services/GradingService.php
   - Method: calculateScorePG(ujian_id, siswa_id)
     - Loop semua jawaban siswa untuk soal PG
     - Bandingkan dengan kunci jawaban
     - Calculate bobot: (correct / total_pg_questions) * 100
     - Save ke Nilai.nilai_otomatis
   
   - Method: calculateScoreEssay(ujian_id, siswa_id, nilai_essay)
     - Take nilai_essay (di-input guru)
     - Return score
   
   - Method: calculateFinalScore(ujian_id, siswa_id)
     - Get nilai_otomatis + nilai_essay
     - Final = (nilai_otomatis + nilai_essay) / 2
     - Determine status: lulus (≥70) / tidak_lulus (<70)
     - Save ke Nilai.nilai_akhir & Nilai.status

2. Guru/NilaiController.php (Manual grading untuk essay)
   - index() → list exam + grade status
   - gradeExam(ujian_id) → show semua siswa + jawaban essay
   - gradeQuestion(ujian_id, soal_id) → grade 1 soal (show all student answers)
   - submitGrade(ujian_id, siswa_id, soal_id, nilai) → save grade
   - publishGrades(ujian_id) → finalize grades (auto-calculate final score)

3. app/Observers/JawabanSiswaObserver.php
   - Jika jawaban submitted & soal adalah PG: auto-calculate skor
   - Jika semua answers submitted: trigger GradingService

Tasks:
1. Create GradingService dengan proper error handling
2. Create Guru/NilaiController untuk essay grading
3. Create views untuk grading interface:
   - resources/views/guru/nilai/index.blade.php (list exam to grade)
   - resources/views/guru/nilai/grade-exam.blade.php (grade form per siswa)
   - resources/views/guru/nilai/view-grades.blade.php (lihat hasil grades)
4. Create API endpoint untuk submit grades (AJAX)

Output: Berikan saya kode GradingService.php yang sudah dibuat.
```

---

# 📍 PHASE 5: Dashboard & Analytics

## ⏱️ Estimasi: 1 jam | Kompleksitas: Medium | Priority: HIGH

### Sub-Prompt 5.1: Statistik Dashboard untuk Admin & Guru

```
Create dashboard dengan statistik nilai per bulan & tahun:

Controllers:
1. Admin/DashboardController.php
   - index() → main dashboard
     - Total guru, siswa, exam, pertanyaan (di tenant ini)
     - Upcoming exams (next 7 days)
     - Recent activities log
     - Button: manage guru, manage siswa, view exams
   
   - statisticsPage() → detailed statistics
     - Charts: nilai per exam (pie chart)
     - Charts: exam trend (line chart) - per bulan
     - Charts: siswa performance (bar chart)
     - Filter: by kategori exam, by bulan, by tahun
     - Data export: CSV, Excel

2. Guru/DashboardController.php
   - index() → main dashboard
     - Total soal created, total exam, total siswa
     - Exam schedule (upcoming + past)
     - Average student score (untuk exam yang sudah selesai)
   
   - statisticsPage() → detailed statistics
     - Chart: nilai siswa per exam (line chart)
     - Chart: soal completion rate (gauge chart)
     - Chart: difficulty analysis (optional - pie chart)
     - Filter: by kategori, by bulan, by tahun
     - Student performance table (sortable, searchable)

3. Siswa/DashboardController.php
   - index() → main dashboard
     - Active exam (happening now)
     - Upcoming exam (next 7 days)
     - History exam (list + scores - NO detail values)
     - Next exam countdown (if any)

Tasks:
1. Create dashboard controllers
2. Create statistics service: app/Services/StatisticsService.php
   - Method: getExamScores(exam_id, bulan?, tahun?)
   - Method: getMonthlyTrend(bulan_mulai, bulan_akhir, tahun)
   - Method: getStudentPerformance(siswa_id)
   - Return: data structure siap untuk chart
3. Create views dengan Chart.js atau similar
4. Add filters: date range picker, dropdown kategori
5. Add export functionality

Output: Berikan saya kode DashboardController.php untuk admin yang sudah dibuat.
```

### Sub-Prompt 5.2: Education News Integration

```
Create news feed integration dari Google News RSS:

Services & Jobs:
1. app/Services/NewsService.php
   - Method: fetchEducationNews(keywords = ['pendidikan', 'kurikulum', 'MTs'], limit = 10)
     - Call Google News RSS: https://news.google.com/rss/search?q=pendidikan+kurikulum
     - Parse RSS feed (use SimpleXML atau GuzzleHttp)
     - Extract: title, description, link, publish_date, source
     - Cache di database (BeritaCache table)
     - Return: array of news
   
   - Method: getCachedNews(tenant_id, limit = 10, expired_in_hours = 1)
     - Get dari database where expires_at > now()
     - Order by published_at DESC
     - If cache empty: call fetchEducationNews() & cache results
   
   - Method: deletExpiredNews()
     - Delete news older than 7 days

2. app/Jobs/FetchEducationNewsJob.php
   - Scheduled job (run setiap 1 jam)
   - Call NewsService::fetchEducationNews()
   - Cache results untuk semua tenant

3. Create controller: Admin/NewsController.php (optional - for manual refresh)
   - refreshNews() → manually trigger fetch
   - manageNews() → view cached news (for moderation)
   - deleteNews(id) → soft delete news item

Tasks:
1. Create NewsService dengan proper error handling & retry logic
2. Create Job & schedule di app/Console/Kernel.php:
   - $schedule->job(new FetchEducationNewsJob)->hourly();
3. Create views untuk show news:
   - resources/views/components/news-feed.blade.php (reusable component)
   - resources/views/admin/news/index.blade.php (manage news)
4. Add news feed component ke semua dashboards:
   - Admin dashboard: right sidebar
   - Guru dashboard: right sidebar
   - Siswa dashboard: below active exams

5. Test with: php artisan schedule:work (untuk local testing)

Output: Berikan saya kode NewsService.php yang sudah dibuat.
```

---

# 📍 PHASE 6: Logo & Identitas Management

## ⏱️ Estimasi: 30 menit | Kompleksitas: Easy | Priority: MEDIUM

### Sub-Prompt 6.1: Logo Upload & Dynamic Display

```
Create functionality untuk Admin ganti logo per tenant:

Controllers:
1. Admin/LogoController.php
   - index() → list semua tenant + logo mereka (preview)
   - edit(tenant_id) → form upload logo untuk tenant
   - update(tenant_id, File) → save logo baru
     - Validate: jpg, jpeg, png, svg, max 1MB
     - Use FileUploadService::uploadLogo()
     - Update LogoIdentitas table
     - Clear cache untuk navbar rendering
   
   - deleteAnyLogo(logo_id) → delete logo, revert ke default

Tasks:
1. Create controller
2. Create views:
   - resources/views/admin/logo/index.blade.php (manage semua tenant logos)
   - resources/views/admin/logo/edit.blade.php (upload form)
   - resources/views/admin/logo/index.blade.php (view current logo)
3. Create helper: app/Helpers/LogoHelper.php
   - Method: getLogoUrl(tenant_id, type = 'navbar')
   - Return: URL atau default logo
4. Update base layout (app.blade.php):
   - Use LogoHelper untuk navbar logo
   - Use favicon dari LogoIdentitas
5. Add asset pipeline untuk cache-busting (optional)

Output: Berikan saya kode LogoHelper.php yang sudah dibuat.
```

---

# 📍 PHASE 7: UI/UX Implementation dengan Tailwind + DaisyUI

## ⏱️ Estimasi: 1.5 jam | Kompleksitas: Medium | Priority: HIGH

### Sub-Prompt 7.1: Base Layout & Navigation Components

```
Create consistent navbar & sidebar untuk semua halaman:

Tasks:
1. Update resources/views/layouts/app.blade.php:
   - Navbar (top):
     - Logo (dari LogoIdentitas)
     - App title
     - User menu (dropdown): profile, settings, logout
     - Responsive: hamburger menu on mobile
   
   - Sidebar (conditional):
     - Show untuk admin, guru
     - Hide untuk siswa (mobile-first)
     - Menu items based on role:
       - Admin: Tenants, Settings, Analytics, Guru, Siswa, Exam, Categories, Logo
       - Guru: Soal, Ujian, Nilai, Profile
     - Collapsible on mobile
     - Active state indicator
   
   - Main content area:
     - Container dengan max-width
     - Breadcrumb navigation
     - Flash messages (success, error, warning)
   
   - Footer (optional):
     - Copyright, links, version

2. Create components:
   - resources/views/components/navbar.blade.php
   - resources/views/components/sidebar.blade.php
   - resources/views/components/breadcrumb.blade.php
   - resources/views/components/alert.blade.php (success/error/warning)
   - resources/views/components/button.blade.php (reusable button)
   - resources/views/components/form-group.blade.php (reusable form field)
   - resources/views/components/stats-card.blade.php (dashboard card)
   - resources/views/components/table.blade.php (reusable table)

3. Create layout for siswa exam:
   - resources/views/layouts/exam.blade.php
   - Fullscreen compatible
   - Minimal distraction
   - Timer visible

4. Update tailwind.config.js:
   - Add DaisyUI configuration
   - Set theme colors (primary, secondary, etc)
   - Configure dark mode (optional)

Tasks to execute:
1. Create all Blade files
2. Use DaisyUI components: btn, input, form-control, card, table, etc
3. Add custom CSS untuk specific styling:
   - resources/css/custom.css
   - Navbar styling
   - Sidebar styling
   - Responsive tweaks
4. Test responsiveness: mobile (375px), tablet (768px), desktop (1280px)

Output: Berikan saya kode navbar.blade.php yang sudah dibuat.
```

### Sub-Prompt 7.2: Form & Table Components Library

```
Create reusable form & table components:

Components to create:
1. Form Components:
   - resources/views/components/form-input.blade.php
     - label, input field, error message, required indicator
     - Props: name, label, value, error, required, type (text/email/password/etc)
   
   - resources/views/components/form-select.blade.php
     - label, select dropdown, error message
     - Props: name, label, options (array), value, error
   
   - resources/views/components/form-textarea.blade.php
     - label, textarea, error message, char counter
   
   - resources/views/components/form-file.blade.php
     - file input, preview (untuk image)
     - Props: name, label, accept, preview_url

2. Table Components:
   - resources/views/components/table-header.blade.php
     - Column headers dengan sort capability
   
   - resources/views/components/table-row.blade.php
     - Standard row
   
   - resources/views/components/table-actions.blade.php
     - Action buttons: edit, delete, view, etc

3. Notification Components:
   - resources/views/components/success-alert.blade.php
   - resources/views/components/error-alert.blade.php
   - resources/views/components/warning-alert.blade.php

4. Modal Components:
   - resources/views/components/modal.blade.php
     - Generic modal dialog
     - Props: title, body, footer (optional)

Tasks:
1. Create semua components dengan Blade syntax
2. Use DaisyUI form elements (input, select, textarea, etc)
3. Make components reusable & DRY
4. Add proper styling & spacing
5. Test components di berbagai pages

Output: Berikan saya kode form-input.blade.php yang sudah dibuat.
```

### Sub-Prompt 7.3: Mobile Responsive Optimization

```
Optimize semua views untuk mobile (smartphone access):

Tasks:
1. Review semua views untuk responsiveness:
   - Sidebar: hidden di mobile, hamburger menu
   - Tables: responsive atau scroll-x di mobile
   - Forms: full-width di mobile
   - Cards: full-width di mobile
   - Buttons: min-height 44px untuk touch

2. Create mobile-specific views (jika perlu):
   - Exam interface: optimize untuk mobile browsers
   - Question display: readable di layar kecil
   - Answer options: clear & easy to tap

3. Add viewport meta tag di layout:
   - <meta name="viewport" content="width=device-width, initial-scale=1">

4. Optimize images:
   - Responsive images: srcset untuk berbagai ukuran
   - Lazy loading: loading="lazy"
   - Compress: use WebP format

5. Test responsiveness:
   - Chrome DevTools: device emulation
   - Real devices: iPhone 6, Android phone
   - Browsers: Chrome, Safari, Firefox, Edge

6. Optimize JavaScript:
   - Minimize & bundle (Vite sudah handle)
   - Lazy load scripts jika perlu
   - Minimize DOM manipulation

Tasks:
1. Run: npm run build (production build)
2. Check bundle size: npm run build -- --report
3. Lighthouse test: DevTools → Lighthouse
4. Fix issues (jika ada)

Output: Berikan hasil screenshot responsiveness test dari 3 halaman berbeda.
```

---

# 📍 PHASE 8: Testing, Deployment & Finishing

## ⏱️ Estimasi: 1 jam | Kompleksitas: Medium | Priority: HIGH

### Sub-Prompt 8.1: Create Test Cases & Run Tests

```
Create automated test untuk critical paths:

Tests to create:
1. Feature Tests:
   - tests/Feature/Auth/GuruLoginTest.php (Google OAuth flow)
   - tests/Feature/Auth/SiswaLoginTest.php (NIS + Password)
   - tests/Feature/Admin/GuruManagementTest.php (CRUD guru)
   - tests/Feature/Exam/ExamCreationTest.php (guru create exam)
   - tests/Feature/Exam/ExamTakingTest.php (siswa take exam)
   - tests/Feature/Grading/AutomaticGradingTest.php (PG grading)

2. Unit Tests:
   - tests/Unit/Services/FileUploadServiceTest.php
   - tests/Unit/Services/GradingServiceTest.php
   - tests/Unit/Services/NewsServiceTest.php

3. Database Tests:
   - Test tenant isolation (queries harus scoped)
   - Test relationships (hasMany, belongsToMany)

Tasks:
1. Use Laravel testing framework (PHPUnit)
2. Create test methods dengan descriptive names
3. Setup test database (use in-memory SQLite untuk speed)
4. Mock external services (Google OAuth, News API)
5. Run tests: php artisan test
6. Generate coverage report: php artisan test --coverage

Output: Berikan saya kode satu test file yang sudah dibuat.
```

### Sub-Prompt 8.2: Environment Setup & Deployment Checklist

```
Prepare untuk production deployment:

Tasks:
1. Create .env.example dari .env (jangan include secrets):
   - Ganti values dengan placeholders
   - Include semua required variables
   - Include comments untuk setiap variable

2. Update .gitignore:
   - /node_modules
   - /vendor
   - .env
   - storage/
   - bootstrap/cache/
   - public/uploads/
   - etc

3. Create deployment checklist:
   ✓ Database migrations tested
   ✓ All tests passing
   ✓ npm run build successful
   ✓ .env production configured
   ✓ Security headers set
   ✓ HTTPS enabled
   ✓ Backup strategy ready
   ✓ Monitoring/logging configured
   ✓ Rate limiting enabled
   ✓ CORS properly configured

4. Create deployment script (optional):
   - scripts/deploy.sh
   - Automate: git pull, composer install, npm install, npm run build, php artisan migrate, cache clear

5. Setup Nginx config:
   - /etc/nginx/sites-available/ujianku-cbt
   - Include SSL cert paths
   - Include proper headers & security rules

Output: Berikan saya checklist lengkap & .env.example yang sudah dibuat.
```

---

## 🎓 Final Notes untuk Developer

**Urutan Eksekusi Phase:**
1. Phase 0 → 1 → 2 → 3 → 4 → 5 → 6 → 7 → 8
2. **Jangan skip** — setiap phase depend pada sebelumnya
3. Test setiap phase sebelum lanjut

**Best Practices:**
- **MANUAL: Commit ke Git setelah setiap phase:**
  ```bash
  git add .
  git commit -m "Phase X: [deskripsi]"
  ```
  (AI Agent HANYA siapkan kode, USER yang LAKUKAN commit manual)
- Jangan commit .env file atau sensitive data
- Write tests seiring dengan development (TDD)
- Code review dengan mentor sebelum merge (jika ada)
- Document complex logic dengan comments

**Resources:**
- Laravel Documentation: https://laravel.com/docs/11
- Tailwind CSS: https://tailwindcss.com/
- DaisyUI: https://daisyui.com/
- stancl/tenancy: https://tenancyforlaravel.com/

**Perlu Bantuan?**
- Jika error: READ error message dengan teliti, jangan langsung copy-paste solusi
- Gunakan AI Agent (Copilot) untuk generate boilerplate, tapi ALWAYS review kodenya
- Jika stuck: ask mentor atau break down masalah jadi smaller pieces

---

**Good Luck! Semoga sukses dengan project ujianku-cbt! 🚀**

---

**Last Updated:** 2026-05-08  
**Next Update:** After Phase completion for refinements