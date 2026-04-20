<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class GymRegistrationController extends Controller
{
    public function __construct(
        protected GymRepositoryInterface $gymRepository
    ) {}

    /**
     * @OA\Post(
     *     path="/gym/registration/step-1",
     *     tags={"Gym App: Provisions"},
     *     summary="Step 1: Operative Identity",
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"name","email","phone"}, @OA\Property(property="name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="phone", type="string"))),
     *     @OA\Response(response=200, description="Identity captured")
     * )
     */
    public function step1(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required','email' => 'required|email|unique:users,email','phone' => 'required|unique:users,phone']);

        try {
            DB::beginTransaction();
            $user = $this->gymRepository->createOperative($request->all());
            $otp = rand(100000, 999999);
            DB::table('gym_otp_verifications')->insert(['email' => $request->email, 'otp' => $otp, 'expires_at' => now()->addMinutes(15), 'created_at' => now()]);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Identity captured. OTP sent.', 'data' => ['user_id' => $user->id, 'otp_preview' => $otp]]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/gym/registration/verify-otp",
     *     tags={"Gym App: Provisions"},
     *     summary="Protocol: Verify Identity Signal",
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"email","otp"}, @OA\Property(property="email", type="string"), @OA\Property(property="otp", type="string"))),
     *     @OA\Response(response=200, description="Authenticated")
     * )
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);

        $user = $this->gymRepository->verifyOtp($request->email, $request->otp);
        if (!$user) return response()->json(['success' => false, 'message' => 'Invalid signal.'], 422);

        return response()->json(['success' => true, 'message' => 'Authenticated.', 'token' => $user->createToken('gym-reg')->plainTextToken, 'user' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/gym/registration/step-2",
     *     tags={"Gym App: Provisions"},
     *     summary="Step 2: Commercial Logic",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"gym_name"},
     *         @OA\Property(property="gym_name", type="string", example="Iron Force Elite"),
     *         @OA\Property(property="template_ids", type="array", @OA\Items(type="string", description="UUIDs of platform membership templates")),
     *         @OA\Property(property="custom_plans", type="array", @OA\Items(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Elite operative"),
     *             @OA\Property(property="price", type="number", example=2999),
     *             @OA\Property(property="tagline", type="string", example="Advanced Tactical Training"),
     *             @OA\Property(property="duration_months", type="integer", example=12),
     *             @OA\Property(property="offer_price", type="number", example=2499),
     *             @OA\Property(property="includes_diet_plan", type="boolean", example=true),
     *             @OA\Property(property="includes_trainer", type="boolean", example=true)
     *         ))
     *     )),
     *     @OA\Response(response=200, description="Plans synced")
     * )
     */
    public function step2(Request $request): JsonResponse
    {
        $request->validate(['gym_name' => 'required']);
        $gym = $this->gymRepository->initiateNode(auth()->user(), $request->all());
        $this->gymRepository->syncNodePlans($gym, $request->template_ids, $request->custom_plans);

        return response()->json(['success' => true, 'message' => 'Plans synchronized.', 'gym_id' => $gym->id]);
    }

    /**
     * @OA\Post(
     *     path="/gym/registration/step-3",
     *     tags={"Gym App: Provisions"},
     *     summary="Step 3: Visual Identity",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\MediaType(mediaType="multipart/form-data", @OA\Schema(
     *         @OA\Property(property="gym_id", type="string", description="UUID of the node"),
     *         @OA\Property(property="image", type="string", format="binary", description="Primary node identification image"),
     *         @OA\Property(property="gallery[]", type="array", @OA\Items(type="string", format="binary", description="Array of gallery portfolio images"))
     *     ))),
     *     @OA\Response(response=200, description="Assets deployed")
     * )
     */
    public function step3(Request $request): JsonResponse
    {
        $request->validate(['gym_id' => 'required|exists:gyms,id']);
        $gym = Gym::findOrFail($request->gym_id);
        $this->gymRepository->uploadNodeAssets($gym, $request->file('image'), $request->file('gallery'));

        return response()->json(['success' => true, 'message' => 'Assets deployed.']);
    }

    /**
     * @OA\Post(
     *     path="/gym/registration/step-4",
     *     tags={"Gym App: Provisions"},
     *     summary="Step 4: Geographic Hub",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"gym_id","latitude","longitude","address"}, @OA\Property(property="gym_id", type="string"), @OA\Property(property="latitude", type="number"), @OA\Property(property="longitude", type="number"), @OA\Property(property="address", type="string"), @OA\Property(property="city", type="string"))),
     *     @OA\Response(response=200, description="Coords synced")
     * )
     */
    public function step4(Request $request): JsonResponse
    {
        $request->validate(['gym_id' => 'required|exists:gyms,id', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'address' => 'required']);
        $gym = Gym::findOrFail($request->gym_id);
        $gym->update($request->only(['latitude', 'longitude', 'address', 'city']));

        return response()->json(['success' => true, 'message' => 'Location calibrated.']);
    }

    /**
     * @OA\Post(
     *     path="/gym/registration/step-5",
     *     tags={"Gym App: Provisions"},
     *     summary="Step 5: Final Intel",
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"gym_id"},
     *         @OA\Property(property="gym_id", type="string"),
     *         @OA\Property(property="description", type="string", example="Elite fitness facility..."),
     *         @OA\Property(property="brand_name", type="string", example="Iron Force"),
     *         @OA\Property(property="custom_fields", type="object", example={"color_code": "#FF0000"})
     *     )),
     *     @OA\Response(response=200, description="Active")
     * )
     */
    public function step5(Request $request): JsonResponse
    {
        $request->validate(['gym_id' => 'required|exists:gyms,id']);
        $gym = Gym::findOrFail($request->gym_id);
        $gym->update($request->only(['description', 'brand_name']));
        if ($request->has('custom_fields')) $gym->saveCustomFields($request->custom_fields);
        $gym->update(['status' => 'active']);

        return response()->json(['success' => true, 'message' => 'Node active.', 'data' => $gym->load(['owner', 'feePlans', 'galleryMedia'])]);
    }
}
