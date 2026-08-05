<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Must run before Booking::casts() gains the 'id_number' => 'encrypted' cast.
 * Encrypts any existing plaintext id_number values via raw query-builder
 * updates (bypassing Eloquent casts entirely) so the model cast never has to
 * decrypt a plaintext value. Skips rows that already decrypt cleanly, so
 * this migration is safe to run more than once.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')->whereNotNull('id_number')->orderBy('id')->chunkById(100, function ($bookings) {
            foreach ($bookings as $booking) {
                try {
                    Crypt::decryptString($booking->id_number);

                    continue; // Already encrypted.
                } catch (\Throwable) {
                    // Plaintext — fall through and encrypt it below.
                }

                DB::table('bookings')->where('id', $booking->id)->update([
                    'id_number' => Crypt::encryptString($booking->id_number),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('bookings')->whereNotNull('id_number')->orderBy('id')->chunkById(100, function ($bookings) {
            foreach ($bookings as $booking) {
                try {
                    $plain = Crypt::decryptString($booking->id_number);
                } catch (\Throwable) {
                    continue; // Already plaintext.
                }

                DB::table('bookings')->where('id', $booking->id)->update(['id_number' => $plain]);
            }
        });
    }
};
