<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Users\app\Interfaces\UserAppRepositoryInterface;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class UserAppRegistrationController extends Controller
{
    public function __construct(
        protected UserAppRepositoryInterface $userAppRepository
    ) {}

    /**
     * @OA\Post(
     *     path="/user-app/registration/step-1",
     *     tags={"User App: Onboarding"},
     *     summary="Step 1: User Identity",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name","email"},
     *         @OA\Property(property="name", type="string", example="John Doe"),
     *         @OA\Property(property="email", type="string", example="john@example.com"),
     *         @OA\Property(property="phone", type="string", example="+1234567890")
     *     )),
     *     @OA\Response(response=200, description="Identity captured. OTP sent.")
     * )
     */
    public function step1(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            $user = $this->userAppRepository->createIdentity($request->all());
            $otp = rand(100000, 999999);
            
            DB::table('gym_otp_verifications')->updateOrInsert(
                ['email' => $request->email],
                ['otp' => $otp, 'expires_at' => now()->addMinutes(15), 'created_at' => now()]
            );
            
            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Identity captured. OTP sent.', 
                'data' => [
                    'user_id' => $user->id, 
                    'otp_preview' => $otp // For testing purposes, remove in production
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/user-app/registration/verify-otp",
     *     tags={"User App: Onboarding"},
     *     summary="Protocol: Verify Identity Signal",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"email","otp"},
     *         @OA\Property(property="email", type="string", example="john@example.com"),
     *         @OA\Property(property="otp", type="string", example="123456")
     *     )),
     *     @OA\Response(response=200, description="Authenticated")
     * )
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email', 
            'otp' => 'required'
        ]);

        $user = $this->userAppRepository->verifyOtp($request->email, $request->otp);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        // Generate Sanctum token
        $token = $user->createToken('user-app')->plainTextToken;

        return response()->json([
            'success' => true, 
            'message' => 'Authenticated.', 
            'token' => $token, 
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/registration/step-2",
     *     tags={"User App: Onboarding"},
     *     summary="Step 2: Physical Attributes",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="gender", type="string", example="male"),
     *         @OA\Property(property="date_of_birth", type="string", format="date", example="1995-05-15"),
     *         @OA\Property(property="height", type="number", example=180.5),
     *         @OA\Property(property="current_weight", type="number", example=75.0),
     *         @OA\Property(property="target_weight", type="number", example=70.0),
     *         @OA\Property(property="blood_group", type="string", example="O+")
     *     )),
     *     @OA\Response(response=200, description="Physical attributes updated")
     * )
     */
    public function step2(Request $request): JsonResponse
    {
        $request->validate([
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'date_of_birth' => 'nullable|date',
            'height' => 'nullable|numeric',
            'current_weight' => 'nullable|numeric',
            'target_weight' => 'nullable|numeric',
            'blood_group' => 'nullable|string|max:5',
        ]);

        $user = auth()->user();
        $this->userAppRepository->updatePhysical($user, $request->only([
            'gender', 'date_of_birth', 'height', 'current_weight', 'target_weight', 'blood_group'
        ]));

        return response()->json([
            'success' => true, 
            'message' => 'Physical attributes synchronized.',
            'user' => $user->load('profile')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/registration/step-3",
     *     tags={"User App: Onboarding"},
     *     summary="Step 3: Goals & Lifestyle",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="fitness_level", type="string", example="intermediate"),
     *         @OA\Property(property="goal_type", type="string", example="weight_loss"),
     *         @OA\Property(property="activity_level", type="string", example="moderately_active"),
     *         @OA\Property(property="diet_type", type="string", example="veg"),
     *         @OA\Property(property="workout_frequency_goal", type="integer", example=4),
     *         @OA\Property(property="preferred_workout_time", type="string", example="morning")
     *     )),
     *     @OA\Response(response=200, description="Goals & Lifestyle updated")
     * )
     */
    public function step3(Request $request): JsonResponse
    {
        $request->validate([
            'fitness_level' => 'nullable|in:beginner,intermediate,advanced,athlete',
            'goal_type' => 'nullable|in:weight_gain,weight_loss,maintenance,muscle_building',
            'activity_level' => 'nullable|in:sedentary,lightly_active,moderately_active,very_active,extra_active',
            'diet_type' => 'nullable|in:veg,non_veg,eggitarian,vegan,keto,paleo',
            'workout_frequency_goal' => 'nullable|integer',
            'preferred_workout_time' => 'nullable|in:morning,afternoon,evening,late_night,flexible',
        ]);

        $user = auth()->user();
        $this->userAppRepository->updateGoals($user, $request->only([
            'fitness_level', 'goal_type', 'activity_level', 'diet_type', 'workout_frequency_goal', 'preferred_workout_time'
        ]));

        return response()->json([
            'success' => true, 
            'message' => 'Goals and lifestyle synchronized.',
            'user' => $user->load('profile')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/registration/step-4",
     *     tags={"User App: Onboarding"},
     *     summary="Step 4: Medical Intel",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="medical_conditions", type="string", example="None"),
     *         @OA\Property(property="allergies", type="string", example="Peanuts"),
     *         @OA\Property(property="physical_limitations", type="string", example="Bad knee")
     *     )),
     *     @OA\Response(response=200, description="Medical intel updated")
     * )
     */
    public function step4(Request $request): JsonResponse
    {
        $request->validate([
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'physical_limitations' => 'nullable|string',
        ]);

        $user = auth()->user();
        $this->userAppRepository->updateMedical($user, $request->only([
            'medical_conditions', 'allergies', 'physical_limitations'
        ]));

        return response()->json([
            'success' => true, 
            'message' => 'Medical intelligence synchronized.',
            'user' => $user->load('profile')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/registration/step-5",
     *     tags={"User App: Onboarding"},
     *     summary="Step 5: Visual Assets & Privacy",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(
     *         @OA\Property(property="profile_image", type="string", format="binary", description="User Avatar"),
     *         @OA\Property(property="is_public", type="boolean", example=true, description="Controls Find Friend visibility")
     *     ))),
     *     @OA\Response(response=200, description="Node active")
     * )
     */
    public function step5(Request $request): JsonResponse
    {
        $request->validate([
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_public' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $isPublic = $request->has('is_public') ? filter_var($request->is_public, FILTER_VALIDATE_BOOLEAN) : null;
        
        $this->userAppRepository->uploadAvatar(
            $user, 
            $request->file('profile_image'), 
            $isPublic
        );

        return response()->json([
            'success' => true, 
            'message' => 'Visuals synchronized. Node active.',
            'user' => $user->load('profile')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user-app/auth/login",
     *     tags={"User App: Auth"},
     *     summary="User App Login",
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"email"},
     *         @OA\Property(property="email", type="string", example="john@example.com")
     *     )),
     *     @OA\Response(response=200, description="OTP sent for login")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $otp = rand(100000, 999999);
            
            DB::table('gym_otp_verifications')->updateOrInsert(
                ['email' => $request->email],
                ['otp' => $otp, 'expires_at' => now()->addMinutes(15), 'created_at' => now()]
            );

            return response()->json([
                'success' => true, 
                'message' => 'Login initiated. OTP sent.', 
                'data' => [
                    'otp_preview' => $otp // For testing purposes, remove in production
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
