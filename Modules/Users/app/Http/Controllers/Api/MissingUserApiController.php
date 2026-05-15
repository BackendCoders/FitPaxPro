<?php

namespace Modules\Users\app\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

/**
 * @OA\Tag(
 *     name="User App Profile",
 *     description="API Endpoints for User Personalization and Training"
 * )
 */
class MissingUserApiController extends Controller
{
    protected $gymRepository;

    public function __construct(GymRepositoryInterface $gymRepository)
    {
        $this->gymRepository = $gymRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/user-app/profile/diet-plans",
     *     summary="Diet Plans",
     *     description="Fetches the assigned meal plan for a member.",
     *     tags={"User App Profile"},
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function dietPlans()
    {
        $data = $this->gymRepository->getDietPlans(auth()->id());
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user-app/profile/exercise-plans",
     *     summary="Exercise Plans",
     *     description="Fetches the weekly workout schedule for a member.",
     *     tags={"User App Profile"},
     *     @OA\Response(response=200, description="Successful response")
     * )
     */
    public function exercisePlans()
    {
        $data = $this->gymRepository->getExercisePlans(auth()->id());
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/fcm/register-token",
     *     summary="FCM Token Registration",
     *     description="Links a device token to a user for push notifications.",
     *     tags={"User App Profile"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "device_name"},
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="device_name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="FCM Token updated.")
     * )
     */
    public function registerFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_name' => 'required|string'
        ]);
        
        $this->gymRepository->registerFcmToken($request->all());
        return response()->json(['success' => true, 'message' => 'FCM Token updated.'], 200);
    }
}
