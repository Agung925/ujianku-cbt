<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('nis', 20)->unique(); // Unique per tenant (enforced via DB)
            $table->string('nama');
            $table->string('email')->nullable()->unique();
            $table->string('password'); // Hashed
            $table->string('foto')->nullable();
            $table->string('kelas', 50);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign key constraint untuk tenant_id
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('nis');
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
