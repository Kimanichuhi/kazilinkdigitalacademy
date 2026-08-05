<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Models\BaseModel;
use Modules\Payment\Database\Factories\MpesaTransactionFactory;
use Modules\Payment\Enums\MpesaTransactionStatus;

class MpesaTransaction extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): MpesaTransactionFactory
    {
        return MpesaTransactionFactory::new();
    }

    protected $fillable = [
        'booking_id', 'purchase_id', 'checkout_request_id', 'merchant_request_id', 'phone', 'amount',
        'status', 'result_code', 'result_desc', 'mpesa_receipt_number',
        'transaction_date', 'raw_callback_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => MpesaTransactionStatus::class,
            'transaction_date' => 'datetime',
            'raw_callback_payload' => 'array',
        ];
    }
}
