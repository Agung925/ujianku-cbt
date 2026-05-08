<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Delete super_admin role - merged into admin role (2026-05-09)
     */
    public function up(): void
    {
        // Delete the super_admin role if it exists
        DB::table('roles')->where('name', 'super_admin')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate super_admin role if needed (for rollback)
        DB::table('roles')->insert([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
