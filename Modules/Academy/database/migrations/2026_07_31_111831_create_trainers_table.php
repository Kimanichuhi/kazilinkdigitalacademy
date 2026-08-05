<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // No FK constraint: profile_id points at users.id, which lives
            // outside this module. Cross-module reads go through
            // Modules\User\Contracts\UserLookupContract, never a join.
            $table->uuid('profile_id')->nullable()->index();
            $table->string('full_name');
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('specializations')->nullable();
            $table->json('social_links')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
