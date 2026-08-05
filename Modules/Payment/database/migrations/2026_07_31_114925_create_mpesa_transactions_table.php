<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Net-new table (not in the source app — see MIGRATION-INVENTORY.md §7.2:
     * the source has zero real payment integration). Tracks one row per STK
     * push attempt so callbacks can be matched idempotently by
     * checkout_request_id and a booking can be retried without duplicating
     * charges.
     */
    public function up(): void
    {
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // booking_id references bookings.id in the Booking module; see
            // Modules\Booking\Contracts\BookingLookupContract for reads.
            $table->uuid('booking_id')->index();
            $table->string('checkout_request_id')->nullable()->unique();
            $table->string('merchant_request_id')->nullable();
            $table->string('phone');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('mpesa_receipt_number')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->json('raw_callback_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
