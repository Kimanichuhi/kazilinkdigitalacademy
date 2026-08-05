<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Prepares mpesa_transactions to back resource-purchase payments (Phase 5)
 * alongside bookings, without a cross-module FK (matches the existing
 * discipline of referencing other modules' ids by plain uuid column only).
 * Exactly one of booking_id/purchase_id should be set per row — enforced at
 * the application level via named constructors, not a DB constraint (no
 * other table in this app uses raw CHECK constraints).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Raw SQL rather than ->change() — doctrine/dbal isn't installed in
        // this project, and this app is MySQL-only.
        DB::statement('ALTER TABLE mpesa_transactions MODIFY booking_id CHAR(36) NULL');

        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->uuid('purchase_id')->nullable()->index()->after('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->dropColumn('purchase_id');
        });

        DB::statement('ALTER TABLE mpesa_transactions MODIFY booking_id CHAR(36) NOT NULL');
    }
};
