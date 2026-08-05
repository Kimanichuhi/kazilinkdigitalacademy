<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index(['is_published', 'published_at']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->index(['is_published', 'order_index']);
            $table->index('rating');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->index(['is_published', 'category', 'order_index']);
            $table->index('is_popular');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->index(['is_published', 'type']);
            $table->index('order_index');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'published_at']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'order_index']);
            $table->dropIndex(['rating']);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'category', 'order_index']);
            $table->dropIndex(['is_popular']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'type']);
            $table->dropIndex(['order_index']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
