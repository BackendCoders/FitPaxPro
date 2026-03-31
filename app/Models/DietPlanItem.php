<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietPlanItem extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'diet_plan_items';

    protected $guarded = [];

    protected $casts = [
        'calories_estimate' => 'integer',
        'order_index' => 'integer',
    ];

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }
}
