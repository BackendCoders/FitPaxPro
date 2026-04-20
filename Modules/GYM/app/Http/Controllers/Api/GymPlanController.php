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
     * Create a new membership plan for a node.
     *
     * @OA\Post(
     *     path="/gym/plans",
     *     tags={"Gym Plans"},
     *     summary="Provision a new commercial plan for a gym node",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gym_id", "name", "price"},
     *             @OA\Property(property="gym_id", type="string", example="uuid-node-id"),
     *             @OA\Property(property="name", type="string", example="Elite operative Pack"),
     *             @OA\Property(property="price", type="number", example=4999),
     *             @OA\Property(property="duration_months", type="integer", example=12)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plan provisioned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
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
