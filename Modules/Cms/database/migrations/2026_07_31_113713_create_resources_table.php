<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type');
            $table->string('file_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_published')->default(true);
            $table->integer('download_count')->default(0);
            $table->json('tags')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
