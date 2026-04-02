<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class AdminAuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin::auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = $this->resolveAdminByEmail($validated['email']);

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided admin credentials are invalid.'],
            ]);
        }

        if (! (bool) $admin->status) {
            throw ValidationException::withMessages([
                'email' => ['Your admin account is inactive.'],
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function dashboard(Request $request)
    {
        return view('admin::dashboard', [
            'admin' => $request->user('admin'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Authenticate an admin API user.
     *
     * @OA\Post(
     *     path="/api/admin/login",
     *     tags={"Admin Auth"},
     *     summary="Authenticate an admin account",
     *     description="Logs in a super admin using email and password and returns a Sanctum bearer token. Super admin users are identified by user_type = 0 in the existing system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password@123"),
     *             @OA\Property(property="device_name", type="string", nullable=true, example="swagger-ui")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Admin login successful."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|sanctumtoken"),
     *                 @OA\Property(
     *                     property="admin",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Super Admin"),
     *                     @OA\Property(property="email", type="string", format="email", example="superadmin@example.com"),
     *                     @OA\Property(property="user_type", type="integer", example=0, description="Super admin user type")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation or authentication error")
     * )
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $admin = $this->resolveAdminByEmail($validated['email']);

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided admin credentials are invalid.'],
            ]);
        }

        if (! (bool) $admin->status) {
            throw ValidationException::withMessages([
                'email' => ['Your admin account is inactive.'],
            ]);
        }

        $token = $admin->createToken($validated['device_name'] ?? 'admin-api')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Admin login successful.',
            'data' => [
                'token' => $token,
                'admin' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'user_type' => (int) $admin->user_type,
                ],
            ],
        ]);
    }

    protected function resolveAdminByEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->get()
            ->first(fn (User $user) => $this->isAdmin($user));
    }

    protected function isAdmin(User $user): bool
    {
        if ((int) $user->user_type === 0) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super-admin', 'super admin']);
    }
}
