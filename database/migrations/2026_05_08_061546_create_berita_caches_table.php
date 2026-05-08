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
        Schema::create('berita_caches', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('title', 255);
            $table->text('description');
            $table->string('source', 100)->nullable(); // Google, Berita, etc
            $table->string('url', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamp('cached_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // When to refresh
            $table->timestamps();
            
            // Foreign key
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('tenant_id');
            $table->index('expires_at'); // For cleanup queries
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_caches');
    }
};
