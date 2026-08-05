<?php

namespace Modules\Cms\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class Purchase extends BaseModel
{
    protected $fillable = [
        'user_id', 'resource_id', 'payment_id', 'amount', 'currency', 'status', 'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'purchased_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
