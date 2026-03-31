<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthLog extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'health_logs';

    protected $guarded = [];

    protected $casts = [
        'weight' => 'decimal:2',
        'bmi' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'log_date' => 'date',
        'water_intake_ml' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
