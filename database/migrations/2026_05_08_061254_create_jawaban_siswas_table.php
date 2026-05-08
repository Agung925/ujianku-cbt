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
        Schema::create('jawaban_siswas', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->text('jawaban')->nullable(); // jawaban pilihan ganda atau essay text
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('ujian_id');
            $table->index('siswa_id');
            $table->index('soal_id');
            $table->index('is_submitted');
            $table->unique(['ujian_id', 'siswa_id', 'soal_id']); // One answer per student per question
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_siswas');
    }
};
