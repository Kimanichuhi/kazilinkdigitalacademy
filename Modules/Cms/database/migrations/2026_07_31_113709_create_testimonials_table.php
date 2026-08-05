<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('student_name');
            $table->string('student_title')->nullable();
            $table->string('student_avatar_url')->nullable();
            $table->text('content');
            $table->decimal('rating', 3, 2)->nullable();
            $table->string('income_before')->nullable();
            $table->string('income_after')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
