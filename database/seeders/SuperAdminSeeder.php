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
     * Create default Super Admin user untuk testing
     * Email: admin@ujianku.test
     * Password: password
     */
    public function run(): void
    {
        // Create or update super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@ujianku.test'],
            [
                'name' => 'Super Admin UJIANKU-CBT',
                'email' => 'admin@ujianku.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign super_admin role
        $superAdmin->syncRoles('super_admin');

        $this->command->info('✅ Super Admin user berhasil dibuat!');
        $this->command->info('   Email: admin@ujianku.test');
        $this->command->info('   Password: password');
    }
}
