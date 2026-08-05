<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('is_free');
            $table->decimal('price', 12, 2)->default(195.00)->after('is_paid');
            $table->string('currency')->default('KES')->after('price');
            $table->unsignedInteger('download_limit')->nullable()->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'price', 'currency', 'download_limit']);
        });
    }
};
