<?php

namespace Modules\Payment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payment\Models\MpesaTransaction;

/**
 * @extends Factory<MpesaTransaction>
 */
class MpesaTransactionFactory extends Factory
{
    protected $model = MpesaTransaction::class;

    public function definition(): array
    {
        return [
            'booking_id' => null,
            'checkout_request_id' => 'ws_CO_'.fake()->unique()->numerify('###############'),
            'merchant_request_id' => (string) fake()->unique()->numberBetween(10000, 99999).'-1-1',
            'phone' => fake()->numerify('2547########'),
            'amount' => fake()->randomElement([9999, 14999, 19999]),
            'status' => 'pending',
            'result_code' => null,
            'result_desc' => null,
            'mpesa_receipt_number' => null,
            'transaction_date' => null,
            'raw_callback_payload' => null,
        ];
    }
}
