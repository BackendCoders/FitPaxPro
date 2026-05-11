<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasCustomFields;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, HasUuid, Notifiable, SoftDeletes, HasCustomFields;

    protected $appends = ['custom_fields_data', 'profile_image_url'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'otp' => 'integer',
        'password' => 'hashed',
        'status' => 'boolean',
        'user_type' => 'integer',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function authoredBlogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function blogComments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function ownedGyms(): HasMany
    {
        return $this->hasMany(Gym::class, 'owner_id');
    }

    public function gymEnquiries(): HasMany
    {
        return $this->hasMany(GymEnquiry::class);
    }

    public function gymReviews(): HasMany
    {
        return $this->hasMany(GymReview::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function healthLogs(): HasMany
    {
        return $this->hasMany(HealthLog::class);
    }

    public function createdDietPlans(): HasMany
    {
        return $this->hasMany(DietPlan::class, 'creator_id');
    }

    public function assignedDietPlans(): HasMany
    {
        return $this->hasMany(DietPlan::class);
    }

    public function createdExercisePlans(): HasMany
    {
        return $this->hasMany(ExercisePlan::class, 'creator_id');
    }

    public function assignedExercisePlans(): HasMany
    {
        return $this->hasMany(ExercisePlan::class);
    }

    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumReplies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function uploadedMedia(): HasMany
    {
        return $this->hasMany(MediaGallery::class, 'uploaded_by');
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }

    public function getProfileImageUrlAttribute(): ?string
    {
        if (!$this->profile_image) {
            return null;
        }

        $path = str_replace('\\', '/', trim((string) $this->profile_image));

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            $path = ltrim(substr($path, strlen('storage/')), '/');
        }

        if (Storage::disk('public')->exists($path)) {
            return route('profile-image.media', ['path' => $path]);
        }

        return route('profile-image.media', ['path' => $path]);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(FcmNotificationLog::class);
    }

    public function warningsIssued(): HasMany
    {
        return $this->hasMany(AdminWarning::class, 'admin_id');
    }

    public function warningsReceived(): HasMany
    {
        return $this->hasMany(AdminWarning::class, 'user_id');
    }

    public function gymSubscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class);
    }
}
