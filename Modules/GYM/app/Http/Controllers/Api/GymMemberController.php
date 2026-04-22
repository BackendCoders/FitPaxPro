<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\GYM\app\Interfaces\GymMemberRepositoryInterface;
use Modules\GYM\app\Http\Requests\StoreGymMemberRequest;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class GymMemberController extends Controller
{
    public function __construct(
        protected GymMemberRepositoryInterface $gymMemberRepository
    ) {
    }

    /**
     * @OA\Get(
     *     path="/gym/members",
     *     tags={"Gym Members"},
     *     summary="List all members for a specific gym",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="gym_id",
     *         in="query",
     *         required=true,
     *         description="UUID of the gym to fetch members for",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Members retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
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

        $perPage = $request->input('per_page', 15);
        $members = $this->gymMemberRepository->getMembersByGym($request->gym_id, $perPage);
        
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

    /**
     * @OA\Post(
     *     path="/gym/members",
     *     tags={"Gym Members"},
     *     summary="Add a new member to a gym",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"gym_id", "name", "phone", "gym_fee_plan_id", "start_date", "amount_paid"},
     *             @OA\Property(property="gym_id", type="string", description="UUID of the gym"),
     *             @OA\Property(property="name", type="string", description="Full name of the member"),
     *             @OA\Property(property="email", type="string", format="email", description="Email of the member (optional)"),
     *             @OA\Property(property="phone", type="string", description="Phone number of the member"),
     *             @OA\Property(property="gym_fee_plan_id", type="string", description="UUID of the selected fee plan"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2026-05-01"),
     *             @OA\Property(property="amount_paid", type="number", example=5000),
     *             @OA\Property(property="payment_method", type="string", example="cash", description="e.g., cash, card, upi"),
     *             @OA\Property(property="notes", type="string", description="Any additional notes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Member added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Member added successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreGymMemberRequest $request): JsonResponse
    {
        try {
            $member = $this->gymMemberRepository->addMember($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Member added successfully.',
                'data' => $member
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/gym/members/{id}",
     *     tags={"Gym Members"},
     *     summary="Get details of a specific gym member subscription",
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="UUID of the member subscription",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Member details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Member subscription not found")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $member = $this->gymMemberRepository->getMemberDetails($id);
            return response()->json([
                'success' => true,
                'data' => $member
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Member not found.'
            ], 404);
        }
    }
}
