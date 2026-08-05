<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ctas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('background_color')->nullable();
            $table->string('background_image_url')->nullable();
            $table->string('button_one_text')->nullable();
            $table->string('button_one_link')->nullable();
            $table->string('button_one_style')->nullable();
            $table->string('button_two_text')->nullable();
            $table->string('button_two_link')->nullable();
            $table->string('button_two_style')->nullable();
            $table->json('placement')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ctas');
    }
};
