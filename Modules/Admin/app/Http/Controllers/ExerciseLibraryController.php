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
            'import_files.*' => 'required|file|max:20480',
        ]);

        $created = 0;
        $skipped = 0;
        $errors = [];
        $files = collect($request->file('import_files'))
            ->filter(fn ($file) => $file && $file->isValid())
            ->sortBy(fn ($file) => $this->normalizeImportPath($file->getClientOriginalName()))
            ->values();

        $supportedDataExtensions = ['json', 'csv', 'txt'];
        $supportedImageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];
        $dataFiles = [];
        $imageFiles = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if (in_array($extension, $supportedDataExtensions, true)) {
                $dataFiles[] = $file;
            } elseif (in_array($extension, $supportedImageExtensions, true)) {
                $imageFiles[] = $file;
            } else {
                $skipped++;
            }
        }

        $batchToken = now()->format('YmdHis') . '-' . Str::random(8);
        $imageIndex = [];

        foreach ($imageFiles as $position => $imageFile) {
            $storedPath = $this->storeImportedImage($imageFile, $batchToken, $position);
            $this->registerImageIndex($imageIndex, $imageFile->getClientOriginalName(), $storedPath);
        }

        foreach ($dataFiles as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $raw = file_get_contents($file->getRealPath());

            $rows = match ($extension) {
                'csv' => $this->parseCsvRows($raw),
                default => $this->parseJsonRows($raw),
            };

            $defaultCategory = $this->deriveCategoryFromPath($file->getClientOriginalName());

            foreach ($rows as $index => $row) {
                try {
                    $payload = $this->normalizeImportedRow($row, $defaultCategory);

                    if (!$payload['exercise_name']) {
                        $skipped++;
                        continue;
                    }

                    if (empty($payload['image_path'])) {
                        $payload['image_path'] = $this->resolveImportedImage($row, $imageIndex, $payload['exercise_name'], $defaultCategory);
                    }

                    $existing = ExerciseLibraryItem::where('exercise_name', $payload['exercise_name'])->first();
                    if ($existing) {
                        $existing->update($payload);
                    } else {
                        ExerciseLibraryItem::create($payload);
                    }

                    $created++;
                } catch (\Throwable $e) {
                    $skipped++;
                    $errors[] = $file->getClientOriginalName() . ' :: Row ' . ($index + 1) . ': ' . $e->getMessage();
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
        $exerciseName = $row['exercise_name'] ?? $row['name'] ?? $row['title'] ?? null;
        $instructions = $row['instructions'] ?? $row['steps'] ?? null;
        $tips = $row['tips'] ?? $row['notes'] ?? null;
        $primaryMuscles = $row['primaryMuscles'] ?? $row['primary_muscles'] ?? $row['target_muscle_group'] ?? null;

        return [
            'exercise_name' => $exerciseName,
            'target_muscle_group' => $this->toDelimitedText($primaryMuscles),
            'exercise_category' => $row['exercise_category'] ?? $row['category'] ?? $defaultCategory,
            'equipment_type' => $this->toDelimitedText($row['equipment_type'] ?? $row['equipment'] ?? null),
            'difficulty_level' => $row['difficulty_level'] ?? $row['difficulty'] ?? null,
            'image_path' => $row['image_path'] ?? $row['image'] ?? $row['image_url'] ?? null,
            'instruction_video_url' => $row['instruction_video_url'] ?? $row['video_url'] ?? null,
            'instructions' => $this->toParagraphText($instructions),
            'tips' => $this->toParagraphText($tips),
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

    private function storeImportedImage($file, string $batchToken, int $position): string
    {
        $relativePath = $this->normalizeImportPath($file->getClientOriginalName());
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $basename = pathinfo($relativePath, PATHINFO_FILENAME);
        $safeName = Str::slug($basename) ?: 'exercise-image';
        $storedName = $position . '-' . Str::random(6) . '-' . $safeName . '.' . $extension;

        return $file->storeAs("exercise-library/imports/{$batchToken}", $storedName, 'public');
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
            $row['slug'] ?? null,
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
}
