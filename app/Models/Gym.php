<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gym extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'gyms';

    protected $guarded = [];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'search_radius_km' => 'integer',
        'is_sponsored' => 'boolean',
        'member_count_limit' => 'integer',
        'rating_avg' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function feePlans(): HasMany
    {
        return $this->hasMany(GymFeePlan::class);
    }

    public function galleryMedia(): HasMany
    {
        return $this->hasMany(GymGalleryMedia::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(GymReview::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(GymEnquiry::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
