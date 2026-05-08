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
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->foreignId('kategori_ujian_id')->constrained('kategori_ujians')->onDelete('cascade');
            $table->string('judul', 255);
            $table->text('deskripsi')->nullable();
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_selesai');
            $table->integer('waktu_durasi'); // in minutes
            $table->boolean('is_acak_soal')->default(false);
            $table->boolean('is_acak_opsi')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('guru_id');
            $table->index('kategori_ujian_id');
            $table->index('is_active');
            $table->index('tgl_mulai');
            $table->index('tgl_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
