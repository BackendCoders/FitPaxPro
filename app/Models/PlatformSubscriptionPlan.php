<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSubscriptionPlan extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'max_gyms' => 'integer',
        'max_members' => 'integer',
        'has_analytics' => 'boolean',
        'has_mobile_app' => 'boolean',
    ];

    public function gyms()
    {
        return $this->hasMany(Gym::class, 'platform_plan_id');
    }
}
