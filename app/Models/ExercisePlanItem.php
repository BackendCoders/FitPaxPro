<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExercisePlanItem extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'exercise_plan_items';

    protected $guarded = [];

    protected $casts = [
        'sets' => 'integer',
        'rest_period_seconds' => 'integer',
        'day_number' => 'integer',
        'order_index' => 'integer',
    ];

    public function exercisePlan(): BelongsTo
    {
        return $this->belongsTo(ExercisePlan::class);
    }
}
