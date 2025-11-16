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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // nama section, optional
            $table->foreignId('content_category_id')->constrained()->cascadeOnDelete(); // relasi ke content category
            $table->integer('order')->default(0); // urutan tampil
            $table->enum('status', ['active', 'inactive'])->default('active'); // status aktif/inaktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
