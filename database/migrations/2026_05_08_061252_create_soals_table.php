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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('kategori_ujian_id')->constrained('kategori_ujians')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('gurus')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->enum('tipe_soal', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->string('kunci_jawaban', 1)->nullable(); // A/B/C/D for pilihan_ganda
            $table->integer('bobot')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('kategori_ujian_id');
            $table->index('guru_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
