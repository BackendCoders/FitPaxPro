<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLibraryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
