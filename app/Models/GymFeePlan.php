<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymFeePlan extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'gym_fee_plans';

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'duration_months' => 'integer',
        'includes_diet_plan' => 'boolean',
        'includes_trainer' => 'boolean',
        'max_gym_visits_per_week' => 'integer',
        'is_active' => 'boolean',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }
}
