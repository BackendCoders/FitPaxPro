<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExerciseLibraryController extends Controller
{
    public function index()
    {
        $exercises = ExerciseLibraryItem::orderBy('order_index')
            ->orderBy('exercise_name')
            ->get();

        return view('admin::exercise-library.index', compact('exercises'));
    }

    public function create()
    {
        return view('admin::exercise-library.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exercise-library', 'public');
        }

        ExerciseLibraryItem::create($data);

        return redirect()->route('admin.exercise-library.index')->with('success', 'Exercise asset added successfully.');
    }

    public function edit(ExerciseLibraryItem $exercise_library)
    {
        return view('admin::exercise-library.edit', ['exercise' => $exercise_library]);
    }

    public function update(Request $request, ExerciseLibraryItem $exercise_library)
    {
        $data = $this->validateRequest($request);

        if ($request->hasFile('image')) {
            if ($exercise_library->image_path) {
                Storage::disk('public')->delete($exercise_library->image_path);
            }

            $data['image_path'] = $request->file('image')->store('exercise-library', 'public');
        }

        $exercise_library->update($data);

        return redirect()->route('admin.exercise-library.index')->with('success', 'Exercise asset updated successfully.');
    }

    public function destroy(ExerciseLibraryItem $exercise_library)
    {
        if ($exercise_library->image_path) {
            Storage::disk('public')->delete($exercise_library->image_path);
        }

        $exercise_library->delete();

        return redirect()->route('admin.exercise-library.index')->with('success', 'Exercise asset removed successfully.');
    }

    public function toggleStatus(ExerciseLibraryItem $exercise_library)
    {
        $exercise_library->update(['is_active' => !$exercise_library->is_active]);

        return response()->json([
            'success' => true,
            'status' => $exercise_library->is_active,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_files' => 'required|array|min:1',
            'import_paths' => 'nullable|array',
        ]);

        $created = 0;
        $skipped = 0;
        $errors = [];
        $uploadedFiles = collect($request->file('import_files'))->values();
        $uploadedPaths = collect($request->input('import_paths', []))->values();

        $files = $uploadedFiles
            ->map(function ($file, $index) use ($uploadedPaths) {
                $path = (string) ($uploadedPaths[$index] ?? $file->getClientOriginalName());

                return [
                    'file' => $file,
                    'path' => $path,
                ];
            })
            ->filter(fn ($entry) => $entry['file'] && $entry['file']->isValid())
            ->sortBy(fn ($entry) => $this->normalizeImportPath($entry['path']))
            ->values();

        $supportedDataExtensions = ['json', 'csv', 'txt'];
        $supportedImageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];
        $dataFiles = [];
        $imageFiles = [];

        foreach ($files as $entry) {
            $file = $entry['file'];
            $path = $entry['path'];
            $extension = strtolower($file->getClientOriginalExtension() ?: pathinfo($path, PATHINFO_EXTENSION));

            if (in_array($extension, $supportedDataExtensions, true)) {
                $dataFiles[] = ['file' => $file, 'path' => $path];
            } elseif (in_array($extension, $supportedImageExtensions, true)) {
                $imageFiles[] = ['file' => $file, 'path' => $path];
            } else {
                $skipped++;
            }
        }

        $batchToken = now()->format('YmdHis') . '-' . Str::random(8);
        $imageIndex = [];

        foreach ($imageFiles as $position => $entry) {
            $storedPath = $this->storeImportedImage($entry['file'], $entry['path'], $batchToken, $position);
            $this->registerImageIndex($imageIndex, $entry['path'], $storedPath);
        }

        foreach ($dataFiles as $entry) {
            $file = $entry['file'];
            $path = $entry['path'];
            $extension = strtolower($file->getClientOriginalExtension() ?: pathinfo($path, PATHINFO_EXTENSION));
            $raw = file_get_contents($file->getRealPath());

            $rows = match ($extension) {
                'csv' => $this->parseCsvRows($raw),
                default => $this->parseJsonRows($raw),
            };

            $defaultCategory = $this->deriveCategoryFromPath($path);
            $isReferenceDataFile = $this->isReferenceDataFile($path, $rows);

            if ($isReferenceDataFile) {
                $skipped += count($rows);
                continue;
            }

            foreach ($rows as $index => $row) {
                try {
                    $payload = $this->normalizeImportedRow($row, $defaultCategory);

                    if (!$payload['exercise_name']) {
                        $skipped++;
                        continue;
                    }

                    $inlineBase64 = $row['base64encoded'] ?? $row['base64_encoded'] ?? null;

                    if (empty($payload['image_path']) && !empty($inlineBase64)) {
                        $payload['image_path'] = $this->storeImportedBase64Image(
                            (string) $inlineBase64,
                            (string) ($payload['source_image_name'] ?? $row['imageName'] ?? $payload['exercise_name']),
                            $batchToken,
                            $index
                        );
                    }

                    if (empty($payload['image_path'])) {
                        $payload['image_path'] = $this->resolveImportedImage($row, $imageIndex, $payload['exercise_name'], $defaultCategory);
                    }

                    $existing = ExerciseLibraryItem::query()
                        ->when(!empty($payload['source_exercise_id']), fn ($query) => $query->where('source_exercise_id', $payload['source_exercise_id']))
                        ->when(empty($payload['source_exercise_id']) && !empty($payload['source_image_name']), fn ($query) => $query->where('source_image_name', $payload['source_image_name']))
                        ->when(empty($payload['source_exercise_id']) && empty($payload['source_image_name']) && !empty($payload['source_slug']), fn ($query) => $query->where('source_slug', $payload['source_slug']))
                        ->when(empty($payload['source_exercise_id']) && empty($payload['source_image_name']) && empty($payload['source_slug']) && !empty($payload['exercise_name']), fn ($query) => $query->where('exercise_name', $payload['exercise_name']))
                        ->first();
                    if ($existing) {
                        $existing->update($payload);
                    } else {
                        ExerciseLibraryItem::create($payload);
                    }

                    $created++;
                } catch (\Throwable $e) {
                    $skipped++;
                    $errors[] = $path . ' :: Row ' . ($index + 1) . ': ' . $e->getMessage();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk import completed.',
            'created' => $created,
            'skipped' => $skipped,
            'errors' => $errors,
        ]);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'exercise_name' => 'required|string|max:255',
            'target_muscle_group' => 'nullable|string|max:100',
            'exercise_category' => 'nullable|string|max:100',
            'equipment_type' => 'nullable|string|max:100',
            'difficulty_level' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'instruction_video_url' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'tips' => 'nullable|string',
            'sets' => 'nullable|integer|min:1|max:100',
            'reps' => 'nullable|string|max:50',
            'rest_period_seconds' => 'nullable|integer|min:0|max:3600',
            'estimated_duration_minutes' => 'nullable|integer|min:1|max:1000',
            'order_index' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
    }

    private function parseJsonRows(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON file uploaded.');
        }

        if (!is_array($decoded)) {
            throw new \RuntimeException('JSON file must contain an array of exercises.');
        }

        if (array_is_list($decoded)) {
            return $decoded;
        }

        foreach (['data', 'items', 'exercises', 'results'] as $key) {
            if (isset($decoded[$key]) && is_array($decoded[$key])) {
                return $decoded[$key];
            }
        }

        return [$decoded];
    }

    private function parseCsvRows(string $raw): array
    {
        $lines = preg_split('/\r\n|\n|\r/', trim($raw));
        if (!$lines || count($lines) < 2) {
            throw new \RuntimeException('CSV file must include a header row and at least one data row.');
        }

        $header = str_getcsv(array_shift($lines));
        $rows = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $values = str_getcsv($line);
            $row = [];

            foreach ($header as $index => $column) {
                $row[trim($column)] = $values[$index] ?? null;
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function normalizeImportedRow(array $row, string $defaultCategory): array
    {
        $exerciseName = $row['exercise_name'] ?? $row['name'] ?? $row['title'] ?? $row['excercise'] ?? $row['exercise'] ?? null;
        $sourceExerciseId = $row['exerciseId'] ?? $row['exercise_id'] ?? null;
        $sourceSlug = $row['slug'] ?? $row['source_slug'] ?? ($exerciseName ? Str::slug((string) $exerciseName) : null);
        $sourceImageName = $row['imageName'] ?? $row['image_name'] ?? null;
        $instructions = $row['instructions'] ?? $row['steps'] ?? null;
        $tips = $row['tips'] ?? $row['notes'] ?? null;
        $primaryMuscles = $row['primaryMuscles'] ?? $row['primary_muscles'] ?? $row['target_muscle_group'] ?? null;
        $secondaryMuscles = $row['secondaryMuscles'] ?? $row['secondary_muscles'] ?? null;
        $bodyParts = $row['bodyParts'] ?? $row['body_parts'] ?? null;
        $equipments = $row['equipments'] ?? $row['equipment'] ?? $row['equipment_type'] ?? null;
        $poseLandmarks = $this->parsePoseLandmarks($row['poseLandmarks'] ?? $row['pose_landmarks'] ?? null);
        $imageWidth = $this->toIntegerOrNull($row['imageWidth'] ?? $row['image_width'] ?? null);
        $imageHeight = $this->toIntegerOrNull($row['imageHeight'] ?? $row['image_height'] ?? null);
        $base64Encoded = $row['base64encoded'] ?? $row['base64_encoded'] ?? null;

        return [
            'exercise_name' => $exerciseName,
            'source_exercise_id' => $sourceExerciseId,
            'source_slug' => $sourceSlug,
            'source_image_name' => $sourceImageName,
            'target_muscle_group' => $this->toDelimitedText($primaryMuscles ?: $bodyParts),
            'body_part' => $this->toDelimitedText($bodyParts),
            'target_muscles_json' => $this->toArrayOrNull($primaryMuscles),
            'secondary_muscles_json' => $this->toArrayOrNull($secondaryMuscles),
            'equipments_json' => $this->toArrayOrNull($equipments),
            'exercise_category' => $row['exercise_category'] ?? $row['category'] ?? $this->toDelimitedText($bodyParts) ?? $defaultCategory,
            'equipment_type' => $this->toDelimitedText($equipments),
            'difficulty_level' => $row['difficulty_level'] ?? $row['difficulty'] ?? null,
            'image_path' => $row['image_path'] ?? $row['image'] ?? $row['image_url'] ?? null,
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'pose_landmarks_json' => $poseLandmarks,
            'instruction_video_url' => $row['instruction_video_url'] ?? $row['video_url'] ?? null,
            'instructions' => $this->toParagraphText($instructions),
            'tips' => $this->toParagraphText($tips) ?: $this->toDelimitedText($secondaryMuscles),
            'sets' => $this->toIntegerOrNull($row['sets'] ?? null),
            'reps' => $row['reps'] ?? null,
            'rest_period_seconds' => $this->toIntegerOrNull($row['rest_period_seconds'] ?? $row['rest'] ?? null),
            'estimated_duration_minutes' => $this->toIntegerOrNull($row['estimated_duration_minutes'] ?? $row['duration_minutes'] ?? null),
            'order_index' => $this->toIntegerOrNull($row['order_index'] ?? $row['order'] ?? 0) ?? 0,
            'is_active' => $this->toBoolean($row['is_active'] ?? $row['status'] ?? true),
        ];
    }

    private function deriveCategoryFromPath(string $path): string
    {
        $normalized = $this->normalizeImportPath($path);
        $segments = array_values(array_filter(explode('/', $normalized)));

        if (count($segments) > 1) {
            return Str::headline(pathinfo($segments[0], PATHINFO_FILENAME)) ?: pathinfo($segments[0], PATHINFO_FILENAME);
        }

        return pathinfo($normalized, PATHINFO_FILENAME);
    }

    private function normalizeImportPath(string $path): string
    {
        return str_replace('\\', '/', trim($path));
    }

    private function storeImportedImage($file, string $sourcePath, string $batchToken, int $position): string
    {
        $relativePath = $this->normalizeImportPath($sourcePath);
        $relativeDirectory = trim(pathinfo($relativePath, PATHINFO_DIRNAME), '.');
        $directory = "exercise-library/imports/{$batchToken}" . ($relativeDirectory ? '/' . $relativeDirectory : '');
        $basename = basename($relativePath);

        return $file->storeAs($directory, $basename, 'public');
    }

    private function storeImportedBase64Image(string $encoded, string $sourceName, string $batchToken, int $position): ?string
    {
        $normalized = $this->normalizeBase64EncodedImage($encoded);

        if ($normalized === null) {
            return null;
        }

        $binary = base64_decode($normalized, true);
        if ($binary === false) {
            return null;
        }

        $extension = strtolower(pathinfo($sourceName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];
        if (!in_array($extension, $allowedExtensions, true)) {
            $extension = 'jpg';
        }

        $baseName = pathinfo($sourceName, PATHINFO_FILENAME) ?: 'exercise-image';
        $safeBaseName = Str::slug($baseName) ?: 'exercise-image';
        $fileName = sprintf('%04d-%s.%s', $position + 1, $safeBaseName, $extension);
        $relativePath = "exercise-library/imports/{$batchToken}/inline-images/{$fileName}";

        Storage::disk('public')->put($relativePath, $binary);

        return $relativePath;
    }

    private function registerImageIndex(array &$index, string $sourcePath, string $storedPath): void
    {
        $normalized = $this->normalizeImportPath($sourcePath);
        $basename = pathinfo($normalized, PATHINFO_FILENAME);
        $keys = [
            Str::slug($normalized),
            Str::slug($basename),
            strtolower($normalized),
            strtolower($basename),
        ];

        foreach ($keys as $key) {
            if ($key) {
                $index[$key] = $storedPath;
            }
        }
    }

    private function resolveImportedImage(array $row, array $imageIndex, string $exerciseName, string $defaultCategory): ?string
    {
        $references = [
            $row['image_path'] ?? null,
            $row['image'] ?? null,
            $row['image_url'] ?? null,
            $row['gifUrl'] ?? null,
            $row['gif_url'] ?? null,
            $row['slug'] ?? null,
            $row['exerciseId'] ?? null,
            $exerciseName,
            $defaultCategory,
        ];

        foreach ($references as $reference) {
            if (!$reference) {
                continue;
            }

            $normalized = $this->normalizeImportPath((string) $reference);
            $basename = pathinfo($normalized, PATHINFO_FILENAME);
            $keys = [
                Str::slug($normalized),
                Str::slug($basename),
                strtolower($normalized),
                strtolower($basename),
            ];

            foreach ($keys as $key) {
                if ($key && isset($imageIndex[$key])) {
                    return $imageIndex[$key];
                }
            }
        }

        return null;
    }

    private function isReferenceDataFile(string $path, array $rows): bool
    {
        $basename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        if (in_array($basename, ['bodyparts', 'equipments', 'muscles'], true)) {
            return true;
        }

        $firstRow = $rows[0] ?? null;
        if (!is_array($firstRow)) {
            return false;
        }

        $keys = array_map('strtolower', array_keys($firstRow));
        $exerciseSignals = [
            'exerciseid',
            'imagename',
            'image_name',
            'base64encoded',
            'base64_encoded',
            'gifurl',
            'instructions',
            'targetmuscles',
            'bodyparts',
            'equipments',
            'secondarymuscles',
            'imagewidth',
            'imageheight',
            'poselandmarks',
            'pose_landmarks',
            'excercise',
            'exercise',
        ];

        if (!array_intersect($keys, $exerciseSignals)) {
            return true;
        }

        return false;
    }

    private function toDelimitedText(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return implode(', ', array_map('trim', array_filter(array_map('strval', $value), fn ($item) => $item !== '')));
        }

        return is_string($value) ? trim($value) : (string) $value;
    }

    private function toParagraphText(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return implode("\n", array_map('trim', array_filter(array_map('strval', $value), fn ($item) => $item !== '')));
        }

        return is_string($value) ? trim($value) : (string) $value;
    }

    private function toIntegerOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    private function toBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'yes', 'active', 'on'], true);
    }

    private function toArrayOrNull(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return array_values(array_filter(array_map(static fn ($item) => is_string($item) ? trim($item) : (string) $item, $value), static fn ($item) => $item !== ''));
        }

        return [is_string($value) ? trim($value) : (string) $value];
    }

    private function normalizeBase64EncodedImage(string $value): ?string
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return null;
        }

        if (preg_match('/^data:image\/[a-z0-9.+-]+;base64,(.*)$/is', $normalized, $matches)) {
            $normalized = $matches[1];
        }

        if (preg_match('/^b([\'"])(.*)\1$/s', $normalized, $matches)) {
            $normalized = $matches[2];
        }

        $normalized = trim($normalized, "\"'");
        $normalized = preg_replace('/\s+/', '', $normalized) ?? $normalized;

        return $normalized !== '' ? $normalized : null;
    }

    private function parsePoseLandmarks(mixed $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_array($value)) {
            return array_values(array_filter(array_map(static fn ($item) => is_string($item) ? trim($item) : (string) $item, $value), static fn ($item) => $item !== ''));
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter(array_map(static fn ($item) => is_string($item) ? trim($item) : (string) $item, $decoded), static fn ($item) => $item !== ''));
        }

        if (preg_match_all('/\'([^\']*)\'/', $text, $matches) && !empty($matches[1])) {
            return array_values(array_filter(array_map('trim', $matches[1]), static fn ($item) => $item !== ''));
        }

        return [$text];
    }
}
