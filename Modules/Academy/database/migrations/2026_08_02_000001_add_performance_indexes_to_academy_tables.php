<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->index(['is_published', 'is_active']);
            $table->index('order_index');
        });

        Schema::table('cohorts', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->index(['is_active', 'is_featured']);
            $table->index('order_index');
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'is_active']);
            $table->dropIndex(['order_index']);
        });

        Schema::table('cohorts', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('trainers', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'is_featured']);
            $table->dropIndex(['order_index']);
        });
    }
};
