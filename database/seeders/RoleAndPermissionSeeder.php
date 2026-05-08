<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Membuat roles dan permissions untuk sistem UJIANKU-CBT
     * Roles: super_admin, admin, guru, siswa
     * Permissions: manage-tenants, manage-users, manage-exams, dll
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== PERMISSIONS =====
        $permissions = [
            // Super Admin Permissions
            'manage-tenants',
            'manage-superadmins',
            'view-all-schools',
            'view-all-exams',

            // Admin Permissions
            'manage-users',
            'manage-teachers', // guru
            'manage-students', // siswa
            'manage-classes',
            'manage-categories', // kategori ujian
            'view-school-reports',
            'export-data',

            // Teacher (Guru) Permissions
            'create-exams',
            'edit-exams',
            'delete-exams',
            'create-questions',
            'edit-questions',
            'delete-questions',
            'manage-question-bank',
            'view-student-answers',
            'grade-essays',
            'view-exam-results',
            'view-student-reports',

            // Student (Siswa) Permissions
            'take-exams',
            'view-own-grades',
            'view-own-exam-history',
            'submit-exam-answers',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ===== ROLES =====

        // Super Admin Role - Full access to everything
        $superAdminRole = Role::findOrCreate('super_admin', 'web');
        $superAdminRole->syncPermissions(Permission::all());

        // Admin Role - Per-tenant admin
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminPermissions = [
            'manage-users',
            'manage-teachers',
            'manage-students',
            'manage-classes',
            'manage-categories',
            'view-school-reports',
            'export-data',
            'create-exams',
            'edit-exams',
            'delete-exams',
            'create-questions',
            'edit-questions',
            'delete-questions',
            'view-exam-results',
            'grade-essays',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Teacher (Guru) Role
        $guruRole = Role::findOrCreate('guru', 'web');
        $guruPermissions = [
            'create-exams',
            'edit-exams',
            'delete-exams',
            'create-questions',
            'edit-questions',
            'delete-questions',
            'manage-question-bank',
            'view-student-answers',
            'grade-essays',
            'view-exam-results',
            'view-student-reports',
        ];
        $guruRole->syncPermissions($guruPermissions);

        // Student (Siswa) Role
        $siswaRole = Role::findOrCreate('siswa', 'web');
        $siswaPermissions = [
            'take-exams',
            'view-own-grades',
            'view-own-exam-history',
            'submit-exam-answers',
        ];
        $siswaRole->syncPermissions($siswaPermissions);

        $this->command->info('✅ Roles dan permissions berhasil dibuat!');
    }
}
