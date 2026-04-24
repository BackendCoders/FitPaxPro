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

    protected $appends = ['custom_fields_data', 'image_url', 'completion_progress'];

    public function getCompletionProgressAttribute()
    {
        $steps = 0;
        
        // Step 1: Operative Identity
        if ($this->owner_id) $steps++;
        
        // Step 2: Commercial Logic
        if ($this->name && $this->feePlans()->exists()) $steps++;
        
        // Step 3: Visual Identity
        if ($this->image || $this->galleryMedia()->exists()) $steps++;
        
        // Step 4: Geographic Hub
        if ($this->latitude && $this->longitude && $this->address) $steps++;
        
        // Step 5: Final Intel
        if ($this->description && $this->status === 'active') $steps++;

        return [
            'steps_completed' => $steps,
            'total_steps' => 5,
            'percentage' => ($steps / 5) * 100,
            'is_complete' => $steps === 5
        ];
    }

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
