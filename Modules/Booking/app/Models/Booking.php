<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Booking\Database\Factories\BookingFactory;
use Modules\Booking\Enums\BookingStatus;
use Modules\Booking\Enums\PaymentStatus;
use Modules\Booking\Services\BookingNumberGenerator;
use Modules\Core\Models\BaseModel;

class Booking extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'program_id', 'cohort_id',
        'full_name', 'email', 'phone', 'id_number', 'date_of_birth', 'gender',
        'nationality', 'address', 'city', 'country', 'county', 'constituency', 'current_occupation',
        'employer', 'education_level',
        'payment_method', 'payment_reference', 'amount_paid', 'total_amount',
        'currency', 'payment_status', 'status',
        'referral_source', 'emergency_contact_name', 'emergency_contact_phone',
        'special_requirements', 'documents_urls', 'admin_notes', 'rejection_reason',
        'consent_given', 'confirmed_at', 'approved_at', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'documents_urls' => 'array',
            'amount_paid' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'consent_given' => 'boolean',
            'confirmed_at' => 'datetime',
            'approved_at' => 'datetime',
            'id_number' => 'encrypted',
        ];
    }

    public function maskedIdNumber(): ?string
    {
        return $this->id_number ? '********'.substr($this->id_number, -4) : null;
    }

    protected static function newFactory(): BookingFactory
    {
        return BookingFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = app(BookingNumberGenerator::class)->next();
            }
        });
    }
}
