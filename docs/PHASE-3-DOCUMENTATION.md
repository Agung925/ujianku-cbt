# PHASE 3: User Management & Profile System

**Completion Date**: 2026-05-08  
**Status**: ✅ COMPLETE  
**Version**: 1.0.0

---

## 📋 Overview

Phase 3 mengimplementasikan sistem manajemen user lengkap dengan profile management, file upload handling, dan role-based access control untuk admin, guru, dan siswa. Sistem ini mencakup CRUD operations dengan validasi komprehensif, soft-delete untuk data integrity, dan responsive UI dengan Tailwind CSS & DaisyUI.

### Key Features
✅ Admin Guru Management - CRUD guru, upload foto, aktivasi/deaktivasi  
✅ Admin Siswa Management - CRUD siswa, bulk create, filter kelas  
✅ Guru Siswa Management - Guru (wali kelas) bulk create siswa  
✅ File Upload Service - Resize foto dengan Intervention Image  
✅ Profile Pictures - Support foto guru & siswa  
✅ Bulk Operations - CSV/Excel import untuk siswa  
✅ Role-Based Views - Different interfaces per role  
✅ Soft Delete - Non-permanent deletion untuk audit trail  

---

## 📊 Architecture

### File Upload Processing

```
User Upload File
    ↓
FileUploadRequest Validation
    ├─ Type: jpg, png, jpeg
    ├─ Size: max 5MB
    └─ Dimensions: min 200x200px
    ↓
FileUploadService::upload()
    ├─ Save original: storage/app/uploads/
    ├─ Generate thumbnail: storage/app/uploads/thumb/
    ├─ Resize with Intervention Image v3
    ├─ Create FileUpload record (polymorphic)
    └─ Return: file URL
    ↓
View: Display foto dari public disk
```

### Database Relationships

```
User
├── Guru (1-1)
│   ├── FileUpload (polymorphic) - Foto guru
│   └── Ujian (1-many) - Exam yang dibuat
│
└── Siswa (1-1)
    ├── FileUpload (polymorphic) - Foto siswa
    └── Nilai (1-many) - Grades
```

---

## 📁 Implementation Details

### 1. FileUploadService (`app/Services/FileUploadService.php`)

**Purpose**: Centralized file handling dengan resize & optimization

**Public Methods**:

```php
public function upload(UploadedFile $file, string $modelType = 'guru', int $modelId = null): FileUpload
// Upload & resize file
// Parameters:
//   - $file: UploadedFile instance
//   - $modelType: 'guru' or 'siswa' (for polymorphic)
//   - $modelId: target model ID
// Returns: FileUpload model instance
// Process:
//   1. Validate file type & size
//   2. Generate unique filename
//   3. Store original: storage/app/uploads/
//   4. Generate thumbnail: storage/app/uploads/thumb/
//   5. Resize with Intervention Image (200x200 → 400x400)
//   6. Create polymorphic FileUpload record
//   7. Return FileUpload model

public function delete(FileUpload $file): bool
// Delete file & record
// Removes from storage + database

public function getUrl(FileUpload $file, string $type = 'original'): string
// Get public URL
// Type: 'original' or 'thumb'
// Returns: asset() URL

public function resize(UploadedFile $file, int $width, int $height): UploadedFile
// Resize image using Intervention Image
// Returns: resized image as UploadedFile
```

### 2. Models

#### Guru Model
```php
class Guru extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;
    
    protected $fillable = [
        'tenant_id', 'user_id', 'nip', 'nama_guru', 'email', 
        'no_hp', 'alamat', 'is_active', 'google_id'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function user(): BelongsTo { }
    public function file(): MorphMany { }  // Foto
    public function ujian(): HasMany { }   // Exam created
}
```

#### Siswa Model
```php
class Siswa extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes;
    
    protected $fillable = [
        'tenant_id', 'user_id', 'nis', 'nama_siswa', 'email',
        'no_hp', 'alamat', 'kelas', 'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public function user(): BelongsTo { }
    public function file(): MorphMany { }  // Foto
    public function nilai(): HasMany { }   // Grades
}
```

#### FileUpload Model
```php
class FileUpload extends Model
{
    use BelongsToTenant, SoftDeletes;
    
    protected $fillable = [
        'tenant_id', 'uploaded_by', 'fileable_type', 'fileable_id',
        'nama_file', 'mime_type', 'ukuran_file', 'path_original',
        'path_thumbnail', 'deskripsi'
    ];
    
    public function fileable(): MorphTo { }
}
```

### 3. Form Requests (Validation)

#### GuruRequest
```php
public function rules(): array
{
    $guruId = $this->route('guru')?->id;
    
    return [
        'nip' => 'required|string|max:20|unique:gurus,nip,' . $guruId . ',id,tenant_id,' . tenancy()->tenant?->id,
        'nama_guru' => 'required|string|max:255',
        'email' => 'required|email|unique:gurus,email,' . $guruId . ',id,tenant_id,' . tenancy()->tenant?->id,
        'no_hp' => 'required|string|max:15',
        'alamat' => 'nullable|string|max:500',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120|dimensions:min_width=200,min_height=200',
        'is_active' => 'boolean',
    ];
}
```

#### SiswaRequest
```php
public function rules(): array
{
    $siswaId = $this->route('siswa')?->id;
    
    return [
        'nis' => 'required|string|max:20|unique:siswas,nis,' . $siswaId . ',id,tenant_id,' . tenancy()->tenant?->id,
        'nama_siswa' => 'required|string|max:255',
        'kelas' => 'required|string|max:10',
        'email' => 'nullable|email|unique:siswas,email,' . $siswaId,
        'no_hp' => 'nullable|string|max:15',
        'alamat' => 'nullable|string|max:500',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:5120|dimensions:min_width=200,min_height=200',
        'is_active' => 'boolean',
    ];
}
```

#### BulkSiswaRequest (CSV Import)
```php
public function rules(): array
{
    return [
        'file' => 'required|file|mimes:csv,txt|max:10240',  // 10MB max
    ];
}
```

### 4. Controllers

#### Admin/GuruController

**Routes & Methods**:

```php
GET     /admin/guru              → index()      // List guru paginated
GET     /admin/guru/create       → create()     // Create form
POST    /admin/guru              → store()      // Save guru
GET     /admin/guru/{guru}       → show()       // View detail
GET     /admin/guru/{guru}/edit  → edit()       // Edit form
PUT     /admin/guru/{guru}       → update()     // Update guru
DELETE  /admin/guru/{guru}       → destroy()    // Soft-delete guru
```

**Key Features**:
- Paginated list (15 per page)
- Search by nama_guru, email, nip
- Filter by is_active status
- Create user account automatically if email not exists
- Assign 'guru' role from spatie/permission
- Upload foto with Intervention Image resize
- Soft-delete on destroy (update is_active = false)
- Cannot delete guru if has active exams

#### Admin/SiswaController

**Routes & Methods**:

```php
GET     /admin/siswa             → index()      // List siswa, filterable
GET     /admin/siswa/create      → create()     // Create form
POST    /admin/siswa             → store()      // Save siswa
GET     /admin/siswa/{siswa}     → show()       // View detail
GET     /admin/siswa/{siswa}/edit → edit()      // Edit form
PUT     /admin/siswa/{siswa}     → update()     // Update siswa
DELETE  /admin/siswa/{siswa}     → destroy()    // Soft-delete siswa
POST    /admin/siswa/import      → import()     // Bulk CSV import
```

**Key Features**:
- Paginated list with filter by kelas + search
- Bulk import dari CSV (NIS, nama_siswa, email, kelas)
- Reset password functionality
- Auto-generate temporary password
- Soft-delete (activate/deactivate)
- Assign 'siswa' role automatically

#### Guru/SiswaManagementController

**Routes & Methods**:

```php
GET     /guru/siswa              → index()      // List guru's students
GET     /guru/siswa/create       → create()     // Create form
POST    /guru/siswa              → store()      // Save siswa
POST    /guru/siswa/bulk-create  → bulkCreate() // Bulk entry form
POST    /guru/siswa/bulk-store   → bulkStore()  // Save bulk siswa
```

**Key Features**:
- Guru (wali kelas) bisa bulk create siswa
- Dynamic form rows dengan JavaScript
- Preview daftar siswa sebelum submit
- Auto-assign to guru's kelas

---

### 5. Views

#### Admin Guru Views

**`admin/guru/index.blade.php`**
- DataTable dengan columns: NIP, Nama, Email, Telepon, Status, Aksi
- Search bar + Active/Inactive filter
- Create button
- Edit/Delete/View action buttons
- Pagination

**`admin/guru/create.blade.php` & `edit.blade.php`**
- Form fields: NIP, Nama Guru, Email, No HP, Alamat
- Foto upload dengan preview
- Active toggle
- Auto-create user account checkbox
- Submit & Cancel buttons

**`admin/guru/show.blade.php`**
- Read-only detail view
- Display foto
- Show user account status
- List exams created by guru
- Edit & Delete buttons

#### Admin Siswa Views

**`admin/siswa/index.blade.php`**
- DataTable dengan columns: NIS, Nama, Email, Kelas, Status, Aksi
- Kelas dropdown filter + search
- Create & Bulk Import buttons
- Edit/Delete/View actions
- Pagination

**`admin/siswa/create.blade.php` & `edit.blade.php`**
- Form fields: NIS, Nama Siswa, Kelas, Email, No HP, Alamat
- Foto upload
- Active toggle
- Reset password button (edit only)
- Submit & Cancel

**`admin/siswa/bulk-create.blade.php`** (Import)
- File upload (CSV)
- Preview rows before import
- Validation feedback
- Import button

#### Guru Siswa Views

**`guru/siswa/index.blade.php`**
- List siswa di kelas guru
- Filter & search
- Create single siswa button
- Bulk create button

**`guru/siswa/bulk-create.blade.php`**
- Dynamic form rows (add/remove via JS)
- Fields: NIS, Nama, Email, No HP per row
- Add Row button
- Preview before submit
- Bulk Create button

---

## 📊 Database Schema

### Gurus Table
```sql
CREATE TABLE gurus (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    user_id BIGINT UNIQUE,
    nip VARCHAR(20) UNIQUE,
    nama_guru VARCHAR(255),
    email VARCHAR(255),
    no_hp VARCHAR(15),
    alamat TEXT,
    google_id VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Siswas Table
```sql
CREATE TABLE siswas (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    user_id BIGINT UNIQUE,
    nis VARCHAR(20) UNIQUE,
    nama_siswa VARCHAR(255),
    email VARCHAR(255),
    no_hp VARCHAR(15),
    alamat TEXT,
    kelas VARCHAR(10),
    is_active BOOLEAN DEFAULT true,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### FileUploads Table
```sql
CREATE TABLE file_uploads (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    uploaded_by BIGINT,
    fileable_type VARCHAR(255),
    fileable_id BIGINT,
    nama_file VARCHAR(255),
    mime_type VARCHAR(50),
    ukuran_file INT,
    path_original VARCHAR(255),
    path_thumbnail VARCHAR(255),
    deskripsi TEXT,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 🧪 Testing & Verification

### Feature Tests

```php
✓ admin_can_create_guru
✓ admin_can_list_guru_paginated
✓ admin_can_edit_guru
✓ admin_can_delete_guru_soft_delete
✓ admin_guru_search_functionality
✓ admin_guru_filter_by_status

✓ admin_can_create_siswa
✓ admin_can_bulk_import_siswa_from_csv
✓ admin_can_edit_siswa
✓ admin_can_reset_siswa_password
✓ admin_siswa_filter_by_kelas
✓ admin_siswa_search_functionality

✓ guru_can_bulk_create_siswa
✓ guru_can_list_own_siswa
✓ file_upload_resize_correctly
✓ soft_delete_maintains_data
✓ multi_tenant_isolation_guru_siswa
```

### Validation Tests

```php
✓ guru_nip_unique_per_tenant
✓ siswa_nis_unique_per_tenant
✓ guru_email_required
✓ siswa_email_optional_but_unique
✓ foto_image_validation
✓ foto_dimensions_minimum
✓ foto_size_max_5mb
```

---

## 🚀 Deployment Checklist

- [x] Models created with relationships
- [x] Controllers with full CRUD
- [x] Form Requests with validation
- [x] Views with Tailwind CSS + DaisyUI
- [x] Routes registered
- [x] FileUploadService implemented
- [x] Intervention Image integration
- [x] Soft-delete for data integrity
- [x] Authorization checks (admin-only, role-based)
- [x] Multi-tenant scoping
- [x] Tests written & passing
- [x] Error handling & logging

---

## ✨ Summary

**Phase 3** provides complete user management system with:
- ✅ CRUD operations untuk guru dan siswa
- ✅ File upload handling dengan resize
- ✅ Bulk operations support
- ✅ Soft-delete untuk audit trail
- ✅ Role-based access control
- ✅ Multi-tenant support
- ✅ Comprehensive validation
- ✅ Full test coverage

**Status**: 🟢 **PRODUCTION READY**

---

**Documentation Version**: 1.0.0  
**Last Updated**: 2026-05-09  
**Scope**: User Management & Profile System
