<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call role and permission seeder first
        $this->call(RoleAndPermissionSeeder::class);
        
        // Create super admin user
        $this->call(SuperAdminSeeder::class);
    }
}
