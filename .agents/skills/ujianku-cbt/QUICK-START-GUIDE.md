# 🚀 UJIANKU-CBT — Quick Start Guide

**Untuk:** Junior Developer menggunakan GitHub Copilot Agent  
**Durasi Total Project:** ~8-10 jam development time  
**Complexity:** Medium (untuk junior developer)

---

## 📋 Pre-Flight Checklist

Pastikan semua ini sudah ready sebelum mulai:

```
✅ Folder project ada di: /workir/www/ujianku-cbt/
✅ PostgreSQL 18 running & accessible
✅ PHP 8.5.5 terinstall
✅ Nginx 1.22.1 configured
✅ Composer installed & working
✅ Node.js + NPM installed
✅ VS Code dengan GitHub Copilot aktif (berbayar)
✅ Git initialized di project folder
✅ .env file sudah ada (copy dari .env.example)
```

Jika ada yang belum, setup dulu sebelum lanjut!

---

## 🎯 Development Workflow

### Step 1: Baca Context Files Dulu (PENTING!)
```
1. Buka file: .agents/skills/ujianku-cbt/SKILL.md
   → Baca KESELURUHAN (30 menit)
   → Ini adalah "brain" project kamu
   
2. Buka file: .agents/skills/ujianku-cbt/PROMPTS-Part-1.md
   → Scan semua prompts (10 menit)
   → Tidak perlu dihafalkan, cukup tau ada apa aja
```

### Step 2: Mulai dari PHASE 0

```
1. Buka VS Code di folder /workir/www/ujianku-cbt
2. Buka GitHub Copilot Chat (Cmd/Ctrl + Shift + I atau Cmd/Ctrl + I)
3. Copy salah satu prompt dari PROMPTS-Part-1.md
4. Paste di Copilot Chat
5. Tekan Enter
6. Tunggu Copilot generate/execute
7. Review hasilnya & test
```

### Step 3: Setiap Phase Selesai

```
1. Test fitur yang baru dibuat (manual testing)
2. Fix errors jika ada
3. Commit ke Git:
   git add .
   git commit -m "Phase X: [deskripsi singkat]"
4. Lanjut ke phase berikutnya
```

---

## 💬 Tips Menggunakan GitHub Copilot Agent

### ✅ DO:
```
✓ Copilot AI Assistant adalah teman baik — gunakan sebaik mungkin
✓ Paste full prompt dari PROMPTS file — jangan ambil sebagian
✓ Jika Copilot generate code → REVIEW sebelum accept
✓ Jika ada error → READ error message dulu, jangan langsung skip
✓ Test setiap fitur sebelum lanjut phase berikutnya
✓ Commit regularly ke Git (jangan tunggu semua selesai)
✓ Tanya Copilot untuk explanation kode yang tidak dimengerti
```

### ❌ DON'T:
```
✗ Jangan asal accept code dari Copilot tanpa review
✗ Jangan skip error/warning messages
✗ Jangan commit .env atau sensitive files
✗ Jangan ubah struktur project tanpa alasan kuat
✗ Jangan mix with prompts dari project lain
✗ Jangan expect Copilot selalu sempurna — ada kalanya perlu manual fix
```

---

## 🔍 Testing Each Phase

### Phase 0: Frontend Setup
```
npm run dev
→ Browser: http://localhost:5173
→ Check: Tailwind working? DaisyUI available?
```

### Phase 1: Database
```
php artisan migrate
→ Check: All tables created?
php artisan tinker
→ Test: App\Models\Guru::all() (should be empty array)
```

### Phase 2: Auth
```
- Test Super Admin login (email + password)
- Test Guru login (Google OAuth)
- Test Siswa login (NIS + password)
→ Session created? Redirect to correct dashboard?
```

### Phase 3+: Features
```
Manual testing setiap CRUD operation:
- Create ✓
- Read ✓
- Update ✓
- Delete ✓
Test di berbagai role (admin, guru, siswa)
Test mobile responsiveness
```

---

## 🚨 Common Issues & Solutions

### Issue: "SQLSTATE[08006] could not connect to server"
**Solution:** PostgreSQL not running atau wrong credentials di .env
```
Check:
- Is PostgreSQL running? (sudo service postgresql status)
- DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD di .env correct?
```

### Issue: "Class not found" errors
**Solution:** Composer autoload belum generate
```
Run: composer dump-autoload
     php artisan cache:clear
```

### Issue: Tailwind CSS not working
**Solution:** Assets belum compiled
```
Run: npm install
     npm run dev
Check: public/build/manifest.json exists?
```

### Issue: Copilot generates incorrect code
**Solution:** Copilot sometimes hallucinates — normal!
```
- Read error message carefully
- Ask Copilot to fix: "Fix this error in the code"
- Or manually edit based on error message
- Test again
```

### Issue: Migrations already exist errors
**Solution:** Database not in sync
```
Run: php artisan migrate:refresh (WARNING: clears all data!)
     php artisan migrate:fresh --seed
```

---

## 📊 Project Structure at a Glance

```
ujianku-cbt/
├── .agents/skills/ujianku-cbt/
│   ├── SKILL.md ← Context file
│   ├── PROMPTS-Part-1.md ← Phase 0-2 prompts
│   └── PROMPTS-Part-2.md ← Phase 3-8 prompts
│
├── app/ ← Laravel application code
│   ├── Models/ ← Database models
│   ├── Http/Controllers/ ← Controllers
│   ├── Http/Requests/ ← Form validation
│   ├── Http/Middleware/ ← Middleware
│   └── Services/ ← Business logic
│
├── database/
│   ├── migrations/ ← Database schemas
│   └── seeders/ ← Seed data
│
├── resources/
│   ├── views/ ← Blade templates (HTML)
│   ├── css/ ← Tailwind CSS
│   └── js/ ← JavaScript (anti-cheat, etc)
│
├── routes/ ← URL routes
│   ├── web.php
│   ├── admin.php
│   ├── guru.php
│   └── siswa.php
│
├── public/ ← Static assets
├── storage/ ← File uploads
└── .env ← Environment config (DON'T COMMIT!)
```

---

## 🎓 Learning Path

Jika belum familiar dengan teknologi, cari & pelajari:

```
WAJIB (sebelum mulai):
- [ ] Laravel basics (Models, Controllers, Migrations, Routes)
- [ ] Blade templating syntax
- [ ] Tailwind CSS (utility-first CSS framework)

RECOMMENDED (sambil development):
- [ ] Laravel relationships (hasMany, belongsToMany)
- [ ] Laravel authorization & authentication
- [ ] Database design & normalization
- [ ] REST API principles

BONUS (jika sempat):
- [ ] Unit testing dengan PHPUnit
- [ ] Security best practices
- [ ] Performance optimization
```

---

## 📞 When You're Stuck

**Prioritas troubleshoot:**

1. **Read error message carefully** (99% punya solusi di error message)
2. **Google the error** (copy paste error ke Google)
3. **Check Laravel documentation** (laravel.com/docs)
4. **Ask GitHub Copilot** (explain error & ask for fix)
5. **Ask mentor/konsultan** (jika sudah stuck >30 menit)

---

## 🎯 Success Metrics

Project bisa dibilang berhasil jika:

```
✅ Setiap role bisa login dengan metode yang sesuai
✅ Super admin bisa manage tenants & logo
✅ Admin bisa manage guru, siswa, exam categories
✅ Guru bisa import soal, create exam, grade siswa
✅ Siswa bisa take exam di mobile dengan anti-cheat
✅ Grades hanya visible ke guru & admin
✅ Dashboard punya statistics & news feed
✅ UI responsive di mobile (tested)
✅ All tests passing (php artisan test)
✅ Zero console errors di browser
```

---

## 🚀 After Project Selesai

```
1. Clean up code & remove debugging
2. Write documentation (README.md)
3. Do final testing (all features)
4. Deploy ke server (atau prepare untuk deploy)
5. Celebrate! 🎉 Anda sudah buat produk siap pasar!
```

---

## 📝 Final Reminders

```
"Code bukan hanya untuk computer — code adalah komunikasi dengan developer lain."

- Write readable code (good variable names, comments in Indonesian)
- Keep it simple (jangan over-engineer)
- Test regularly (jangan surprise bugs di akhir)
- Commit often (git history yang jelas)
- Ask questions (tidak ada yang terlalu bodoh untuk ditanya)
```

---

## 📞 Contact Points

**Konsultan IT Development:** Available untuk:
- Code review
- Architecture explanation
- Performance optimization
- Security assessment
- Emergency debugging

**Don't hesitate to ask!** 💬

---

**Good luck! Project mu bakalan bagus! 🔥**

---

**Last Updated:** 2026-05-08