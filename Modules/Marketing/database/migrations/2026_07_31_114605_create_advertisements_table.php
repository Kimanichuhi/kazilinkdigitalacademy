<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('campaign_name');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('type');
            $table->json('placement');
            $table->string('desktop_image_url')->nullable();
            $table->string('mobile_image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->string('button_style')->nullable();
            $table->string('background_color')->nullable();
            $table->string('overlay_color')->nullable();
            $table->string('animation')->nullable();
            $table->integer('priority')->default(0);
            $table->json('target_audience')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
