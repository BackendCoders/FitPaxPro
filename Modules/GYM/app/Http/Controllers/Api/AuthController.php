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
     * @OA\Post(
     *     path="/gym/register",
     *     tags={"Gym Auth"},
     *     summary="Register a new gym mobile account",
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"name","email","password"}, @OA\Property(property="name", type="string"), @OA\Property(property="email", type="string"), @OA\Property(property="password", type="string"))),
     *     @OA\Response(response=201, description="Registered")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ])->validate();

        $result = $this->authRepository->register($validated);

        return response()->json(['success' => true, 'message' => 'Registered.', 'data' => $result], 201);
    }

    /**
     * Login Protocol: Initiate OTP
     * 
     * @OA\Post(
     *     path="/gym/login",
     *     tags={"Gym Auth"},
     *     summary="Initiate login via OTP signal",
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"email"}, @OA\Property(property="email", type="string", example="ops@fitpaxpro.com"))),
     *     @OA\Response(response=200, description="OTP Sent")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required']);
        $otp = $this->authRepository->sendOtp($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Login signal transmission successful. Please verify OTP.',
            'otp_preview' => $otp // Temp for tactical testing
        ]);
    }

    /**
     * Login Protocol: Verify OTP
     * 
     * @OA\Post(
     *     path="/gym/login/verify",
     *     tags={"Gym Auth"},
     *     summary="Complete login via OTP verification",
     *     @OA\RequestBody(required=true, @OA\JsonContent(required={"email", "otp"}, @OA\Property(property="email", type="string"), @OA\Property(property="otp", type="string"))),
     *     @OA\Response(response=200, description="Login successful")
     * )
     */
    public function verifyLogin(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required', 'otp' => 'required']);
        $result = $this->authRepository->verifyLoginOtp($request->email, $request->otp);

        return response()->json([
            'success' => true,
            'message' => 'Identity validated. Login successful.',
            'data' => $result
        ]);
    }
}
