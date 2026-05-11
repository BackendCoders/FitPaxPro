<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExerciseLibraryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User App: Exercise Library",
 *     description="Mobile exercise library APIs with advanced filtering"
 * )
 */
class ExerciseLibraryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user-app/exercises",
     *     tags={"User App: Exercise Library"},
     *     summary="List exercises with filters",
     *     description="Returns the exercise library for the mobile app with search, filter, sort, and pagination support.",
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string"), description="Search by exercise name, category, muscle group, or equipment"),
     *     @OA\Parameter(name="category", in="query", required=false, @OA\Schema(type="string"), description="Filter by exercise category"),
     *     @OA\Parameter(name="muscle_group", in="query", required=false, @OA\Schema(type="string"), description="Filter by target muscle group"),
     *     @OA\Parameter(name="body_part", in="query", required=false, @OA\Schema(type="string"), description="Filter by body part"),
     *     @OA\Parameter(name="equipment", in="query", required=false, @OA\Schema(type="string"), description="Filter by equipment type"),
     *     @OA\Parameter(name="difficulty", in="query", required=false, @OA\Schema(type="string"), description="Filter by difficulty level"),
     *     @OA\Parameter(name="source_exercise_id", in="query", required=false, @OA\Schema(type="string"), description="Filter by source exercise ID from imported dataset"),
     *     @OA\Parameter(name="source_slug", in="query", required=false, @OA\Schema(type="string"), description="Filter by source slug from imported dataset"),
     *     @OA\Parameter(name="active_only", in="query", required=false, @OA\Schema(type="boolean", default=true), description="Only show active exercises"),
     *     @OA\Parameter(name="has_image", in="query", required=false, @OA\Schema(type="boolean"), description="Filter exercises that have an image or GIF"),
     *     @OA\Parameter(name="has_video", in="query", required=false, @OA\Schema(type="boolean"), description="Filter exercises that have a video URL"),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string", enum={"exercise_name","order_index","created_at","updated_at"}), description="Field to sort by"),
     *     @OA\Parameter(name="sort_direction", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc"), description="Sort direction"),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20), description="Items per page"),
     *     @OA\Response(
     *         response=200,
     *         description="Exercise list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = ExerciseLibraryItem::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('exercise_name', 'like', "%{$search}%")
                    ->orWhere('target_muscle_group', 'like', "%{$search}%")
                    ->orWhere('exercise_category', 'like', "%{$search}%")
                    ->orWhere('equipment_type', 'like', "%{$search}%")
                    ->orWhere('difficulty_level', 'like', "%{$search}%")
                    ->orWhere('instructions', 'like', "%{$search}%")
                    ->orWhere('tips', 'like', "%{$search}%");
            });
        }

        $this->applyExactFilter($query, 'exercise_category', $request->input('category'));
        $this->applyExactFilter($query, 'target_muscle_group', $request->input('muscle_group'));
        $this->applyExactFilter($query, 'body_part', $request->input('body_part'));
        $this->applyExactFilter($query, 'equipment_type', $request->input('equipment'));
        $this->applyExactFilter($query, 'difficulty_level', $request->input('difficulty'));

        if ($request->filled('source_exercise_id')) {
            $query->where('source_exercise_id', trim((string) $request->input('source_exercise_id')));
        }

        if ($request->filled('source_slug')) {
            $query->where('source_slug', Str::slug((string) $request->input('source_slug')));
        }

        if ($request->filled('has_image')) {
            $hasImage = $request->boolean('has_image');
            $query->when($hasImage, fn ($builder) => $builder->whereNotNull('image_path')->where('image_path', '!=', ''), fn ($builder) => $builder->where(function ($inner) {
                $inner->whereNull('image_path')->orWhere('image_path', '');
            }));
        }

        if ($request->filled('has_video')) {
            $hasVideo = $request->boolean('has_video');
            $query->when($hasVideo, fn ($builder) => $builder->whereNotNull('instruction_video_url')->where('instruction_video_url', '!=', ''), fn ($builder) => $builder->where(function ($inner) {
                $inner->whereNull('instruction_video_url')->orWhere('instruction_video_url', '');
            }));
        }

        $sortBy = in_array($request->input('sort_by', 'order_index'), ['exercise_name', 'order_index', 'created_at', 'updated_at'], true)
            ? $request->input('sort_by', 'order_index')
            : 'order_index';
        $sortDirection = $request->input('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, min((int) $request->input('per_page', 20), 100));

        $items = $query
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('exercise_name')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/exercises/{identifier}",
     *     tags={"User App: Exercise Library"},
     *     summary="Get exercise details",
     *     description="Fetch a single exercise by UUID or name slug for mobile detail screens.",
     *     @OA\Parameter(name="identifier", in="path", required=true, @OA\Schema(type="string"), description="Exercise UUID or slugified name"),
     *     @OA\Response(
     *         response=200,
     *         description="Exercise detail"
     *     ),
     *     @OA\Response(response=404, description="Exercise not found")
     * )
     */
    public function show(string $identifier): JsonResponse
    {
        $exercise = ExerciseLibraryItem::query()
            ->where('id', $identifier)
            ->orWhere('source_exercise_id', $identifier)
            ->orWhere('source_slug', Str::slug($identifier))
            ->orWhereRaw('LOWER(exercise_name) = ?', [mb_strtolower($identifier)])
            ->first();

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/exercises/filters",
     *     tags={"User App: Exercise Library"},
     *     summary="Get available filters",
     *     description="Returns the distinct filter values available in the exercise library for mobile UI filter chips and dropdowns.",
     *     @OA\Response(
     *         response=200,
     *         description="Available filters"
     *     )
     * )
     */
    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $this->distinctValues('exercise_category'),
                'muscle_groups' => $this->distinctValues('target_muscle_group'),
                'body_parts' => $this->distinctValues('body_part'),
                'equipments' => $this->distinctValues('equipment_type'),
                'difficulty_levels' => $this->distinctValues('difficulty_level'),
            ],
        ]);
    }

    private function applyExactFilter($query, string $column, mixed $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $query->where($column, 'like', '%' . trim((string) $value) . '%');
    }

    private function distinctValues(string $column): array
    {
        return ExerciseLibraryItem::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->filter()
            ->values()
            ->all();
    }
}
