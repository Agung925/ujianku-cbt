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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('file_name', 255);
            $table->string('file_path', 255); // Storage path
            $table->string('file_type', 50); // mime type category: image, document, video, etc
            $table->string('mime_type', 50);
            $table->integer('size'); // in bytes
            $table->string('uploadable_type')->nullable(); // Morphable class name (Guru, Siswa, Soal)
            $table->unsignedBigInteger('uploadable_id')->nullable(); // Morphable ID
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
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
            $table->index(['uploadable_type', 'uploadable_id']); // Polymorphic index
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};
