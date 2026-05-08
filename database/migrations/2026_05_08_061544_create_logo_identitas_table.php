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
        Schema::create('logo_identitas', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('nama_file', 255);
            $table->string('path', 255); // Storage path
            $table->enum('file_type', ['favicon', 'navbar_logo', 'sidebar_logo', 'other']);
            $table->string('mime_type', 50);
            $table->integer('size'); // in bytes
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('set null');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->softDeletes();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('file_type');
            $table->unique(['tenant_id', 'file_type']); // One logo per type per tenant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logo_identitas');
    }
};
