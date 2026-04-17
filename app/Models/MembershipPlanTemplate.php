<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipPlanTemplate extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'duration_months' => 'integer',
        'includes_diet_plan' => 'boolean',
        'includes_trainer' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array',
    ];
}
