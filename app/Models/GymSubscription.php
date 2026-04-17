<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymSubscription extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'id',
        'gym_id',
        'user_id',
        'gym_fee_plan_id',
        'start_date',
        'end_date',
        'amount_paid',
        'payment_status',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(GymFeePlan::class, 'gym_fee_plan_id');
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && now()->between($this->start_date, $this->end_date);
    }
}
