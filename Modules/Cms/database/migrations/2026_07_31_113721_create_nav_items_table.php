<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nav_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_id')->nullable()->constrained('nav_menus')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('nav_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('target')->default('_self');
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('badge')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
