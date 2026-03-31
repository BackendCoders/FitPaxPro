<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DietPlan extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'diet_plans';

    protected $guarded = [];

    protected $casts = [
        'total_calories_target' => 'integer',
        'protein_grams' => 'integer',
        'carbs_grams' => 'integer',
        'fats_grams' => 'integer',
        'is_template' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DietPlanItem::class);
    }
}
