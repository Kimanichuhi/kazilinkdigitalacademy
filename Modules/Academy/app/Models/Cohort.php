<?php

namespace Modules\Academy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academy\Database\Factories\CohortFactory;
use Modules\Core\Models\BaseModel;

class Cohort extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): CohortFactory
    {
        return CohortFactory::new();
    }

    protected $fillable = [
        'program_id', 'trainer_id', 'name', 'start_date', 'end_date', 'registration_deadline',
        'total_seats', 'booked_seats', 'schedule_details', 'schedule_json', 'venue',
        'venue_address', 'online_link', 'online_platform', 'price', 'currency', 'status',
        'is_featured', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_deadline' => 'date',
            'schedule_json' => 'array',
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function seatsLeft(): int
    {
        return max(0, $this->total_seats - $this->booked_seats);
    }
}
