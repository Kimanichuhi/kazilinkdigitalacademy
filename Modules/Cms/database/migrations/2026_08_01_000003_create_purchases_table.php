<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('resource_id')->constrained('resources')->cascadeOnDelete();
            // payment_id references mpesa_transactions.id in the Payment module —
            // no cross-module FK, matching the existing discipline (see
            // mpesa_transactions.booking_id).
            $table->uuid('payment_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('KES');
            $table->string('status')->default('pending');
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
