<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('location')->nullable()->after('student_title');
            $table->string('course_completed')->nullable()->after('location');
            $table->string('achievement')->nullable()->after('course_completed');
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['location', 'course_completed', 'achievement']);
        });
    }
};
