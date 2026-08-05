<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $order = [
            'Home' => 0,
            'About' => 1,
            'Programs' => 2,
            'Cohorts' => 3,
            'Pricing' => 4,
            'Success Stories' => 5,
            'Blog' => 6,
            'Resources' => 7,
            'FAQ' => 8,
            'Contact' => 9,
        ];

        foreach ($order as $label => $index) {
            DB::table('nav_items')->where('label', $label)->update(['order_index' => $index]);
        }
    }

    public function down(): void
    {
        // No-op: original order is not worth restoring.
    }
};
