<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * MySQL 8 has no CREATE SEQUENCE (MariaDB does, but we target both per
     * the brief), so booking numbers are generated from a single locked
     * counter row instead of replicating the source's Postgres sequence —
     * see Modules\Booking\Services\BookingNumberGenerator.
     */
    public function up(): void
    {
        Schema::create('booking_number_counters', function (Blueprint $table) {
            $table->tinyInteger('id')->primary();
            $table->unsignedBigInteger('next_value')->default(1);
        });

        DB::table('booking_number_counters')->insert(['id' => 1, 'next_value' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_number_counters');
    }
};
