<?php

namespace Modules\Booking\Services;

use Illuminate\Support\Facades\DB;

/**
 * Generates booking_number values like BK000123, matching the source's
 * `'BK' || LPAD(nextval('booking_number_seq')::text, 6, '0')` format
 * without relying on Postgres/MariaDB-only sequences (portable to MySQL 8).
 */
class BookingNumberGenerator
{
    public function next(): string
    {
        $number = DB::transaction(function (): int {
            $row = DB::table('booking_number_counters')->where('id', 1)->lockForUpdate()->first();
            $next = $row->next_value;

            DB::table('booking_number_counters')->where('id', 1)->update(['next_value' => $next + 1]);

            return $next;
        });

        return 'BK'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
