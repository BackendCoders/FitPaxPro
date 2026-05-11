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
     *     @OA\Parameter(name="has_gallery", in="query", required=false, @OA\Schema(type="boolean"), description="Filter exercises that have more than one linked image"),
     *     @OA\Parameter(name="has_video", in="query", required=false, @OA\Schema(type="boolean"), description="Filter exercises that have a video URL"),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string", enum={"exercise_name","order_index","created_at","updated_at"}), description="Field to sort by"),
     *     @OA\Parameter(name="sort_direction", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="asc"), description="Sort direction"),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=20), description="Items per page"),
     *     @OA\Response(
     *         response=200,
     *         description="Exercise list",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="f44d1d89-2d58-4fe5-8ae7-0fd1a6a4f7d0"),
     *                     @OA\Property(property="exercise_name", type="string", example="Prisoner Squat - Bodyweight"),
     *                     @OA\Property(property="source_match_key", type="string", example="prisonersquatbodyweight"),
     *                     @OA\Property(property="source_exercise_id", type="string", example="d16cec81-500f-4a0b-8f51-ee2efe91ecee"),
     *                     @OA\Property(property="source_slug", type="string", example="prisoner-squat-bodyweight"),
     *                     @OA\Property(property="source_image_name", type="string", example="full-body-person-doing-pushup0.jpeg"),
     *                     @OA\Property(property="image_path", type="string", example="exercise-library/imports/20260511123000-abcd1234/inline-images/0001-push-up.jpeg"),
     *                     @OA\Property(property="image_url", type="string", example="https://example.com/exercise-library/media/exercise-library/imports/.../0001-push-up.jpeg"),
     *                     @OA\Property(property="image_urls", type="array", @OA\Items(type="string", example="https://example.com/exercise-library/media/exercise-library/imports/.../0001-push-up.jpeg")),
     *                     @OA\Property(property="image_width", type="integer", example=183),
     *                     @OA\Property(property="image_height", type="integer", example=275),
     *                     @OA\Property(property="pose_landmarks_json", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="target_muscle_group", type="string", example="glute, quad, thigh - inner"),
     *                     @OA\Property(property="body_part", type="string", example="lower body"),
     *                     @OA\Property(property="exercise_category", type="string", example="strength"),
     *                     @OA\Property(property="equipment_type", type="string", example="bodyweight")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=200)
     *             )
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

        if ($request->filled('has_gallery')) {
            $hasGallery = $request->boolean('has_gallery');
            $query->when($hasGallery, fn ($builder) => $builder->whereNotNull('image_paths_json')->whereJsonLength('image_paths_json', '>', 1), fn ($builder) => $builder->where(function ($inner) {
                $inner->whereNull('image_paths_json')->orWhereJsonLength('image_paths_json', '<=', 1);
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
     *         description="Exercise detail",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="exercise_name", type="string", example="Prisoner Squat - Bodyweight"),
     *                 @OA\Property(property="source_match_key", type="string", example="prisonersquatbodyweight"),
     *                 @OA\Property(property="source_exercise_id", type="string", example="d16cec81-500f-4a0b-8f51-ee2efe91ecee"),
     *                 @OA\Property(property="source_slug", type="string", example="prisoner-squat-bodyweight"),
     *                 @OA\Property(property="source_image_name", type="string", example="full-body-person-doing-pushup0.jpeg"),
     *                 @OA\Property(property="image_path", type="string", example="exercise-library/imports/.../0001-prisoner-squat-bodyweight.jpeg"),
     *                 @OA\Property(property="image_url", type="string", example="https://example.com/exercise-library/media/exercise-library/imports/.../0001-prisoner-squat-bodyweight.jpeg"),
     *                 @OA\Property(property="image_urls", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="image_width", type="integer", example=183),
     *                 @OA\Property(property="image_height", type="integer", example=275),
     *                 @OA\Property(property="pose_landmarks_json", type="array", @OA\Items(type="string"))
     *             )
     *         )
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
