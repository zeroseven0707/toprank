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
        Schema::create('contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('crawl_data_id');
            $table->foreignId('category_content_id');
            $table->foreignId('city');
            $table->string('name');
            $table->integer('rank');
            $table->text('addreess')->nullable();
            $table->text('maps_link')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->text('url');
            $table->string('period', 7)->comment('format Y-m, contoh: 2025-11');
            $table->boolean('published')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
