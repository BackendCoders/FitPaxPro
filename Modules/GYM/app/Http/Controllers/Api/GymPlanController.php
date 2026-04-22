<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;
use App\Models\GymFeePlan;
use OpenApi\Annotations as OA;

class GymPlanController extends Controller
{
    public function __construct(
        protected GymRepositoryInterface $gymRepository
    ) {}

    /**
     * Get all plans for a specific gym.
     *
     * @OA\Get(
     *     path="/gym/plans",
     *     tags={"Gym Plans"},
     *     summary="List all commercial plans for a gym",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="gym_id",
     *         in="query",
     *         description="UUID of the gym to fetch plans for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plans retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="a19a67fe-d3e7-4e86-b737-48f3a8f14632"),
     *                     @OA\Property(property="gym_id", type="string", example="a19a67fe-d358-4b65-9eed-0d386213dfb8"),
     *                     @OA\Property(property="name", type="string", example="Elite Pack"),
     *                     @OA\Property(property="tagline", type="string", example="Get Fit"),
     *                     @OA\Property(property="description", type="string", example="Full access plan"),
     *                     @OA\Property(property="price", type="number", example=4999),
     *                     @OA\Property(property="offer_price", type="number", example=3999),
     *                     @OA\Property(property="duration_months", type="integer", example=12),
     *                     @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                     @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                     @OA\Property(property="max_gym_visits_per_week", type="integer", example=7, nullable=true),
     *                     @OA\Property(property="image", type="string", example="https://yourdomain.com/storage/gyms/plans/image.jpg", nullable=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'gym_id' => 'required|uuid|exists:gyms,id'
        ]);

        $plans = $this->gymRepository->getPlansByGymId($request->gym_id);
        
        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Get details of a specific plan.
     *
     * @OA\Get(
     *     path="/gym/plans/{id}",
     *     tags={"Gym Plans"},
     *     summary="Get details of a specific gym plan",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of the plan",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="a19a67fe-d3e7-4e86-b737-48f3a8f14632"),
     *                 @OA\Property(property="gym_id", type="string", example="a19a67fe-d358-4b65-9eed-0d386213dfb8"),
     *                 @OA\Property(property="name", type="string", example="Elite Pack"),
     *                 @OA\Property(property="tagline", type="string", example="Get Fit"),
     *                 @OA\Property(property="description", type="string", example="Full access plan"),
     *                 @OA\Property(property="price", type="number", example=4999),
     *                 @OA\Property(property="offer_price", type="number", example=3999),
     *                 @OA\Property(property="duration_months", type="integer", example=12),
     *                 @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                 @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                 @OA\Property(property="max_gym_visits_per_week", type="integer", example=7, nullable=true),
     *                 @OA\Property(property="image", type="string", example="https://yourdomain.com/storage/gyms/plans/image.jpg", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Plan not found")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $plan = $this->gymRepository->getPlanById($id);
            return response()->json([
                'success' => true,
                'data' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan not found.'
            ], 404);
        }
    }

    /**
     * Create a new membership plan for a node.
     *
     * @OA\Post(
     *     path="/gym/plans",
     *     tags={"Gym Plans"},
     *     summary="Provision a new commercial plan for a gym node",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"gym_id", "name", "price"},
     *                 @OA\Property(property="gym_id", type="string", example="a19a67fe-d358-4b65-9eed-0d386213dfb8"),
     *                 @OA\Property(property="name", type="string", example="Elite operative Pack"),
     *                 @OA\Property(property="price", type="number", example=4999),
     *                 @OA\Property(property="tagline", type="string", example="Best plan"),
     *                 @OA\Property(property="description", type="string", example="Includes everything"),
     *                 @OA\Property(property="duration_months", type="integer", example=12),
     *                 @OA\Property(property="offer_price", type="number", example=3999),
     *                 @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                 @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                 @OA\Property(property="max_gym_visits_per_week", type="integer", example=7),
     *                 @OA\Property(property="image", type="string", format="binary", description="Plan cover image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan provisioned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Commercial plan provisioned successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="a19a67fe-d3e7-4e86-b737-48f3a8f14632"),
     *                 @OA\Property(property="gym_id", type="string", example="a19a67fe-d358-4b65-9eed-0d386213dfb8"),
     *                 @OA\Property(property="name", type="string", example="Elite operative Pack"),
     *                 @OA\Property(property="tagline", type="string", example="Best plan"),
     *                 @OA\Property(property="description", type="string", example="Includes everything"),
     *                 @OA\Property(property="price", type="number", example=4999),
     *                 @OA\Property(property="offer_price", type="number", example=3999),
     *                 @OA\Property(property="duration_months", type="integer", example=12),
     *                 @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                 @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                 @OA\Property(property="max_gym_visits_per_week", type="integer", example=7, nullable=true),
     *                 @OA\Property(property="image", type="string", example="https://yourdomain.com/storage/gyms/plans/image.jpg", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'gym_id' => 'required|exists:gyms,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'tagline' => 'nullable|string',
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer',
            'offer_price' => 'nullable|numeric',
            'includes_diet_plan' => 'nullable|boolean',
            'includes_trainer' => 'nullable|boolean',
        ]);

        $plan = $this->gymRepository->createPlan($request->all());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms/plans', 'public');
            $plan->update(['image' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commercial plan provisioned successfully.',
            'data' => $plan
        ], 201);
    }

    /**
     * Update an independent plan node.
     *
     * @OA\Post(
     *     path="/gym/plans/{id}",
     *     tags={"Gym Plans"},
     *     summary="Update a commercial plan for a gym node (Use POST with _method=PUT for multipart)",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of the plan",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="_method", type="string", example="PUT", description="Required for multipart/form-data in Laravel"),
     *                 @OA\Property(property="name", type="string", example="Elite operative Pack V2"),
     *                 @OA\Property(property="price", type="number", example=5999),
     *                 @OA\Property(property="tagline", type="string", example="Updated plan"),
     *                 @OA\Property(property="description", type="string", example="Includes everything updated"),
     *                 @OA\Property(property="duration_months", type="integer", example=12),
     *                 @OA\Property(property="offer_price", type="number", example=4999),
     *                 @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                 @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                 @OA\Property(property="max_gym_visits_per_week", type="integer", example=7),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="image", type="string", format="binary", description="Plan cover image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plan parameters calibrated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Plan parameters calibrated."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="string", example="a19a67fe-d3e7-4e86-b737-48f3a8f14632"),
     *                 @OA\Property(property="gym_id", type="string", example="a19a67fe-d358-4b65-9eed-0d386213dfb8"),
     *                 @OA\Property(property="name", type="string", example="Elite operative Pack V2"),
     *                 @OA\Property(property="tagline", type="string", example="Updated plan"),
     *                 @OA\Property(property="description", type="string", example="Includes everything updated"),
     *                 @OA\Property(property="price", type="number", example=5999),
     *                 @OA\Property(property="offer_price", type="number", example=4999),
     *                 @OA\Property(property="duration_months", type="integer", example=12),
     *                 @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *                 @OA\Property(property="includes_trainer", type="boolean", example=true),
     *                 @OA\Property(property="max_gym_visits_per_week", type="integer", example=7, nullable=true),
     *                 @OA\Property(property="image", type="string", example="https://yourdomain.com/storage/gyms/plans/image.jpg", nullable=true),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $plan = $this->gymRepository->getPlanById($id);
        
        $plan->update($request->all());

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('gyms/plans', 'public');
            $plan->update(['image' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Plan parameters calibrated.',
            'data' => $plan
        ]);
    }
}
