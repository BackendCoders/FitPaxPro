<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Concerns\HasCustomFields;

class GymFeePlan extends Model
{
    use HasFactory, HasUuid, SoftDeletes, HasCustomFields;

    protected $table = 'gym_fee_plans';

    protected $guarded = [];

    protected $appends = ['image_url'];

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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }
}
