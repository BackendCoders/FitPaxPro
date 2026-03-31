<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;
use Modules\GYM\app\Contracts\AuthRepositoryInterface;

class AuthController extends Controller
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository
    ) {
    }

    /**
     * Register a gym mobile user.
     *
     * @OA\Post(
     *     path="/api/gym/register",
     *     tags={"Gym Auth"},
     *     summary="Register a new gym mobile account",
     *     description="Creates a new user record and returns a Sanctum token for mobile app authentication.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+919876543210"),
     *             @OA\Property(property="password", type="string", format="password", example="Password@123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Password@123"),
     *             @OA\Property(property="device_name", type="string", nullable=true, example="iPhone 15"),
     *             @OA\Property(property="user_type", type="integer", nullable=true, example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|abc123token"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="phone", type="string", nullable=true, example="+919876543210")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'user_type' => ['nullable', 'integer', 'in:1,2,3'],
        ])->validate();

        $result = $this->authRepository->register($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => $result,
        ], 201);
    }

    /**
     * Login a gym mobile user.
     *
     * @OA\Post(
     *     path="/api/gym/login",
     *     tags={"Gym Auth"},
     *     summary="Authenticate a gym mobile account",
     *     description="Logs in using either email or phone and returns a Sanctum token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="email", type="string", format="email", nullable=true, example="john@example.com"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+919876543210"),
     *             @OA\Property(property="password", type="string", format="password", example="Password@123"),
     *             @OA\Property(property="device_name", type="string", nullable=true, example="iPhone 15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|abc123token")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation or authentication error")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'email' => ['nullable', 'required_without:phone', 'email'],
            'phone' => ['nullable', 'required_without:email', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $result = $this->authRepository->login($validated);

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => $result,
        ]);
    }
}
