<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymReview extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'gym_reviews';

    protected $guarded = [];

    protected $casts = [
        'rating' => 'integer',
        'anonymous_review' => 'boolean',
        'is_featured' => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(Gym::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
