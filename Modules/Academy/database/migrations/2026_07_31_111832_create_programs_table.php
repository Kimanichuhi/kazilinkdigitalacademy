<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->nullable()->constrained('program_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->json('gallery_urls')->nullable();
            $table->integer('duration_weeks')->nullable();
            $table->string('duration_label')->nullable();
            $table->string('level')->default('beginner');
            $table->string('delivery_mode')->default('online');
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->string('currency')->default('KES');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('enrollment_count')->default(0);
            $table->json('curriculum')->nullable();
            $table->json('outcomes')->nullable();
            $table->json('requirements')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
