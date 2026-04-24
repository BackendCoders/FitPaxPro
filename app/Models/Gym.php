<?php

namespace App\Models;

use App\Models\Concerns\HasCustomFields;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gym extends Model
{
    use HasFactory, HasUuid, SoftDeletes, HasCustomFields;

    protected $appends = ['custom_fields_data', 'image_url'];

    protected $table = 'gyms';

    protected $guarded = [];

    /**
     * Get the platform subscription plan for this gym.
     */
    public function platformPlan()
    {
        return $this->belongsTo(PlatformSubscriptionPlan::class, 'platform_plan_id');
    }

    protected static function booted()
    {
        static::saving(function ($gym) {
            if (empty($gym->slug)) {
                $gym->slug = \Illuminate\Support\Str::slug($gym->name);

                // Ensure uniqueness
                $count = static::where('slug', 'like', $gym->slug . '%')->where('id', '!=', $gym->id)->count();
                if ($count > 0) {
                    $gym->slug .= '-' . ($count + 1);
                }
            }
        });
    }

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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'gym_category');
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
