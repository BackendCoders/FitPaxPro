<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Users\app\Interfaces\UserAppRepositoryInterface;
use OpenApi\Annotations as OA;

class UserAppProfileController extends Controller
{
    public function __construct(
        protected UserAppRepositoryInterface $userAppRepository
    ) {}

    /**
     * @OA\Post(
     *     path="/user-app/profile/measurements",
     *     tags={"User App: Profile"},
     *     summary="Log historical body measurements",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="weight", type="number", example=75.5),
     *         @OA\Property(property="chest", type="number", example=102.0),
     *         @OA\Property(property="waist", type="number", example=85.0),
     *         @OA\Property(property="hips", type="number", example=95.0),
     *         @OA\Property(property="biceps", type="number", example=38.5),
     *         @OA\Property(property="thighs", type="number", example=60.0),
     *         @OA\Property(property="body_fat_percentage", type="number", example=15.2)
     *     )),
     *     @OA\Response(response=200, description="Measurement logged successfully")
     * )
     */
    public function logMeasurement(Request $request): JsonResponse
    {
        $request->validate([
            'weight' => 'nullable|numeric',
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'hips' => 'nullable|numeric',
            'biceps' => 'nullable|numeric',
            'thighs' => 'nullable|numeric',
            'body_fat_percentage' => 'nullable|numeric',
        ]);

        $measurement = $this->userAppRepository->logBodyMeasurement(
            auth()->user(), 
            $request->only(['weight', 'chest', 'waist', 'hips', 'biceps', 'thighs', 'body_fat_percentage'])
        );

        return response()->json([
            'success' => true, 
            'message' => 'Measurement logged successfully.',
            'data' => $measurement
        ]);
    }
}
