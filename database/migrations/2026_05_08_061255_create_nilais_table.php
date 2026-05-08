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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->float('nilai_otomatis')->nullable(); // pilihan ganda
            $table->float('nilai_essay')->nullable(); // essay (di-input guru)
            $table->float('nilai_akhir')->nullable(); // computed: (nilai_otomatis + nilai_essay) / 2
            $table->enum('status', ['lulus', 'tidak_lulus', 'pending'])->default('pending');
            $table->text('catatan_guru')->nullable();
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
            $table->index('status');
            $table->unique(['ujian_id', 'siswa_id']); // One grade per student per exam
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
