<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 🎓 UJIANKU-CBT — Computer-Based Test Platform

**Status**: Active Development (Phase 4.1)  
**Framework**: Laravel 13.8.0  
**Database**: PostgreSQL 18.3  
**Frontend**: Tailwind CSS v3.4 + DaisyUI v4.12

> Platform Computer-Based Test (CBT) multi-tenant modern untuk institusi pendidikan dengan fitur anti-cheat dan management soal yang komprehensif.

---

## 🚀 Quick Start

### Prerequisites
```bash
- PHP 8.5.5+
- PostgreSQL 18.3+
- Node.js 18+ (npm)
- Composer latest
```

### Setup Project
```bash
# 1. Install dependencies
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate --seed

# 4. Build frontend assets
npm run dev

# 5. Start development server
php artisan serve
```

### 🔐 Default Testing Credentials

**Admin Account:**
```
Email:    admin@ujianku.test
Password: password
Role:     admin
```

Access admin panel at: `http://localhost:8000/admin/dashboard`

---

## 📚 Documentation

- **Full Documentation**: See `/docs/` folder
- **Architecture**: `.agents/skills/ujianku-cbt/SKILL.md`
- **Quick Guide**: `.agents/skills/ujianku-cbt/QUICK-START-GUIDE.md`
- **Phase Documentation**: `docs/PHASE-*.md`

---

## 🏗️ Project Structure

```
ujianku-cbt/
├── app/                    # Laravel application code
│   ├── Models/            # Database models
│   ├── Http/Controllers/  # Controllers (Super Admin, Admin, Guru, Siswa)
│   ├── Http/Requests/     # Form validation
│   └── Http/Middleware/   # Custom middleware
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── views/             # Blade templates
│   ├── css/               # Tailwind CSS
│   └── js/                # JavaScript
├── docs/                  # Documentation
└── .agents/skills/        # AI Agent context files
```

---

## 🎯 Features

- ✅ Multi-tenant support (stancl/tenancy)
- ✅ Role-based access control (admin, guru, siswa)
- ✅ Google OAuth integration for guru login
- ✅ Exam category management (Phase 4.1)
- ✅ Question bank with ownership tracking
- ✅ Anti-cheat mechanisms
- ✅ DaisyUI responsive design
- ⏳ In Development: Exam execution, grading, analytics

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
