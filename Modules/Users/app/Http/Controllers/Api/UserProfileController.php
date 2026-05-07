<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Users\app\Interfaces\UserProfileRepositoryInterface;
use OpenApi\Annotations as OA;

class UserProfileController extends Controller
{
    public function __construct(
        protected UserProfileRepositoryInterface $profileRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/user-app/profile",
     *     tags={"User App: Profile"},
     *     summary="Get the full profile of the authenticated user",
     *     description="Fetches comprehensive user data including name, phone, and detailed health/fitness profile.",
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200, 
     *         description="User profile data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $this->profileRepository->getProfile($request->user());

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/profile",
     *     tags={"User App: Profile"},
     *     summary="Update the profile of the authenticated user",
     *     description="Updates user basic information and health/fitness profile fields.",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="phone", type="string", example="+919876543210"),
     *         @OA\Property(property="gender", type="string", enum={"male", "female", "other", "prefer_not_to_say"}),
     *         @OA\Property(property="age", type="integer", example=25),
     *         @OA\Property(property="date_of_birth", type="string", format="date", example="1999-01-01"),
     *         @OA\Property(property="current_weight", type="number", example=75.5),
     *         @OA\Property(property="target_weight", type="number", example=70.0),
     *         @OA\Property(property="height", type="number", example=180.0),
     *         @OA\Property(property="goal_type", type="string", enum={"weight_gain", "weight_loss", "maintenance", "muscle_building"}),
     *         @OA\Property(property="diet_type", type="string", enum={"veg", "non_veg", "eggitarian", "vegan", "keto", "paleo"}),
     *         @OA\Property(property="is_public", type="boolean", example=true)
     *     )),
     *     @OA\Response(
     *         response=200, 
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|max:2048',
            'gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'age' => 'nullable|integer|min:1|max:120',
            'date_of_birth' => 'nullable|date',
            'current_weight' => 'nullable|numeric|min:20|max:500',
            'target_weight' => 'nullable|numeric|min:20|max:500',
            'height' => 'nullable|numeric|min:50|max:300',
            'goal_type' => 'nullable|string|in:weight_gain,weight_loss,maintenance,muscle_building',
            'diet_type' => 'nullable|string|in:veg,non_veg,eggitarian,vegan,keto,paleo',
            'is_public' => 'nullable|boolean',
        ]);

        $user = $this->profileRepository->updateProfile($request->user(), $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }
}
