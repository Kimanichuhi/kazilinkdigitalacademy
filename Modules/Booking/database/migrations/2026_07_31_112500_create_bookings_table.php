<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_number')->unique();
            // FK constraints kept at the DB level for referential integrity;
            // app code never joins across modules — see
            // Modules\Academy\Contracts\ProgramLookupContract /
            // Modules\User\Contracts\UserLookupContract for cross-module reads.
            $table->uuid('user_id')->nullable()->index();
            $table->foreignUuid('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignUuid('cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('id_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Kenya');
            $table->string('current_occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('education_level')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->string('currency')->default('KES');
            $table->string('payment_status')->default('pending');
            $table->string('status')->default('draft');

            $table->string('referral_source')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->text('special_requirements')->nullable();
            $table->json('documents_urls')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('consent_given')->default(false);
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
