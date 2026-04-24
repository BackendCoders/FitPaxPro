<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Users\app\Interfaces\GymListingRepositoryInterface;
use OpenApi\Annotations as OA;

class GymListingController extends Controller
{
    public function __construct(
        protected GymListingRepositoryInterface $gymListingRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/user-app/gyms",
     *     tags={"User App: Gym Discovery"},
     *     summary="List all public active gyms",
     *     description="Retrieve a paginated list of active gyms, sorted by sponsored status and rating.",
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string"), description="Search by name, brand, address, or city"),
     *     @OA\Parameter(name="city", in="query", required=false, @OA\Schema(type="string"), description="Filter by exact city"),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer"), description="Items per page (default: 15)"),
     *     @OA\Response(response=200, description="List of gyms")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'city']);
        $perPage = (int) $request->input('per_page', 15);

        $gyms = $this->gymListingRepository->getActiveGyms($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $gyms
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/gyms/{identifier}",
     *     tags={"User App: Gym Discovery"},
     *     summary="Get details of a specific gym",
     *     description="Retrieve comprehensive details of an active gym by its ID or Slug. Includes fee plans, gallery media, and recent reviews.",
     *     @OA\Parameter(name="identifier", in="path", required=true, @OA\Schema(type="string"), description="Gym ID or Slug"),
     *     @OA\Response(response=200, description="Gym details"),
     *     @OA\Response(response=404, description="Gym not found or inactive")
     * )
     */
    public function show(string $identifier): JsonResponse
    {
        $gym = $this->gymListingRepository->getGymDetails($identifier);

        if (!$gym) {
            return response()->json([
                'success' => false,
                'message' => 'Gym not found or is currently inactive.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $gym
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/gyms/{identifier}/plans",
     *     tags={"User App: Gym Discovery"},
     *     summary="Get pricing plans of a specific gym",
     *     description="Retrieve all active fee/pricing plans for a specific gym.",
     *     @OA\Parameter(name="identifier", in="path", required=true, @OA\Schema(type="string"), description="Gym ID or Slug"),
     *     @OA\Response(response=200, description="List of pricing plans")
     * )
     */
    public function plans(string $identifier): JsonResponse
    {
        $plans = $this->gymListingRepository->getGymPlans($identifier);

        if ($plans->isEmpty() && !\App\Models\Gym::where('id', $identifier)->orWhere('slug', $identifier)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gym not found or has no active plans.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }
}
