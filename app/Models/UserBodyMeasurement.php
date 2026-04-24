<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBodyMeasurement extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'user_body_measurements';

    protected $guarded = [];

    protected $casts = [
        'recorded_at' => 'date',
        'weight' => 'decimal:2',
        'chest' => 'decimal:2',
        'waist' => 'decimal:2',
        'hips' => 'decimal:2',
        'biceps' => 'decimal:2',
        'thighs' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
