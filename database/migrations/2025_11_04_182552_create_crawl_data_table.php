<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crawl_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('url_crawl_id')->constrained('url_crawls')->cascadeOnDelete();
            $table->foreignId('category');
            $table->foreignId('city');
            $table->text('caption')->nullable();
            $table->string('ownerFullName')->nullable();
            $table->string('ownerUsername')->nullable();
            $table->string('url')->unique();
            $table->integer('commentsCount')->default(0);
            $table->text('firstComment')->nullable();
            $table->integer('likesCount')->default(0);
            $table->timestamp('timestamp')->nullable();
            $table->json('hashtags')->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->string('period', 7)->comment('format Y-m, contoh: 2025-11');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_data');
    }
};
