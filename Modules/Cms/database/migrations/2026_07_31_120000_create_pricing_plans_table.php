<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('tag')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('KES');
            $table->string('period')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->string('cta_text')->default('Get Started');
            $table->string('cta_link')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
