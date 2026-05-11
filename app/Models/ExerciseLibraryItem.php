<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

        $path = str_replace('\\', '/', trim($this->image_path));

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return asset('storage/' . $path);
        }

        $basename = basename($path);

        foreach ([
            'exercise-library/imports',
            'exercise-library',
        ] as $prefix) {
            $candidate = trim($prefix . '/' . $basename, '/');

            if ($disk->exists($candidate)) {
                return asset('storage/' . $candidate);
            }
        }

        $matchedPath = $this->findImportedAssetByBasename($basename);

        if ($matchedPath) {
            return asset('storage/' . $matchedPath);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    private function findImportedAssetByBasename(string $basename): ?string
    {
        static $basenameIndex = null;

        if ($basenameIndex === null) {
            $basenameIndex = [];

            foreach (['exercise-library/imports', 'exercise-library'] as $directory) {
                foreach (Storage::disk('public')->allFiles($directory) as $file) {
                    $basenameIndex[basename($file)] ??= $file;
                }
            }
        }

        return $basenameIndex[$basename] ?? null;
    }
}
