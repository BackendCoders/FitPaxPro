<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseLibraryItem extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'exercise_library_items';

    protected $fillable = [
        'exercise_name',
        'target_muscle_group',
        'exercise_category',
        'equipment_type',
        'difficulty_level',
        'image_path',
        'instruction_video_url',
        'instructions',
        'tips',
        'sets',
        'reps',
        'rest_period_seconds',
        'estimated_duration_minutes',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'sets' => 'integer',
        'rest_period_seconds' => 'integer',
        'estimated_duration_minutes' => 'integer',
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }
}
