<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignUuid('trainer_id')->nullable()->constrained('trainers')->nullOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('registration_deadline')->nullable();
            $table->integer('total_seats')->default(0);
            $table->integer('booked_seats')->default(0);
            $table->text('schedule_details')->nullable();
            $table->json('schedule_json')->nullable();
            $table->string('venue')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('online_link')->nullable();
            $table->string('online_platform')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency')->default('KES');
            $table->string('status')->default('upcoming');
            $table->boolean('is_featured')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
