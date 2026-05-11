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
        'source_exercise_id',
        'source_slug',
        'source_match_key',
        'source_image_name',
        'target_muscle_group',
        'body_part',
        'target_muscles_json',
        'secondary_muscles_json',
        'equipments_json',
        'exercise_category',
        'equipment_type',
        'difficulty_level',
        'image_path',
        'image_paths_json',
        'image_width',
        'image_height',
        'pose_landmarks_json',
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
        'target_muscles_json' => 'array',
        'secondary_muscles_json' => 'array',
        'equipments_json' => 'array',
        'image_paths_json' => 'array',
        'pose_landmarks_json' => 'array',
    ];

    protected $appends = [
        'image_url',
        'image_urls',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        $path = str_replace('\\', '/', trim($this->image_path));

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($path, PHP_URL_PATH) ?: '';
            $storageSegment = '/storage/';

            if (str_contains($parsedPath, $storageSegment)) {
                $relativePath = ltrim(substr($parsedPath, strpos($parsedPath, $storageSegment) + strlen($storageSegment)), '/');

                if ($relativePath !== '') {
                    return route('exercise-library.media', ['path' => $relativePath]);
                }
            }

            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            $relativePath = ltrim(substr($path, strlen('storage/')), '/');

            if ($relativePath !== '') {
                return route('exercise-library.media', ['path' => $relativePath]);
            }
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return route('exercise-library.media', ['path' => $path]);
        }

        $basename = basename($path);

        foreach ([
            'exercise-library/imports',
            'exercise-library',
        ] as $prefix) {
            $candidate = trim($prefix . '/' . $basename, '/');

            if ($disk->exists($candidate)) {
                return route('exercise-library.media', ['path' => $candidate]);
            }
        }

        $matchedPath = $this->findImportedAssetByBasename($basename);

        if ($matchedPath) {
            return route('exercise-library.media', ['path' => $matchedPath]);
        }

        return route('exercise-library.media', ['path' => ltrim($path, '/')]);
    }

    public function getImageUrlsAttribute(): array
    {
        $paths = $this->image_paths_json ?? [];

        if (!is_array($paths)) {
            $paths = [];
        }

        if ($this->image_path) {
            array_unshift($paths, $this->image_path);
        }

        $paths = array_values(array_unique(array_filter($paths)));

        return array_values(array_filter(array_map(fn ($path) => $this->resolveMediaUrl($path), $paths)));
    }

    private function resolveMediaUrl(string $path): ?string
    {
        $path = str_replace('\\', '/', trim($path));

        if ($path === '') {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return route('exercise-library.media', ['path' => ltrim($path, '/')]);
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
