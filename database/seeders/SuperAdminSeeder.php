<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Create default Admin user untuk testing
     * Email: admin@ujianku.test
     * Password: password
     * Role: admin (not super_admin)
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@ujianku.test'],
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin@ujianku.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role (not super_admin)
        $admin->syncRoles('admin');

        $this->command->info('✅ Admin user berhasil dibuat!');
        $this->command->info('   Email: admin@ujianku.test');
        $this->command->info('   Password: password');
        $this->command->info('   Role: admin');
    }
}
