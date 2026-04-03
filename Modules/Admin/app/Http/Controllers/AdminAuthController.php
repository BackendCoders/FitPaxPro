<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
            'dashboardData' => $this->buildDashboardData(),
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

    protected function buildDashboardData(): array
    {
        $modules = collect(File::directories(base_path('Modules')))
            ->map(function (string $modulePath): array {
                $manifestPath = $modulePath.DIRECTORY_SEPARATOR.'module.json';
                $routeBasePath = $modulePath.DIRECTORY_SEPARATOR.'routes'.DIRECTORY_SEPARATOR;
                $manifest = File::exists($manifestPath)
                    ? json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR)
                    : [];

                $name = $manifest['name'] ?? basename($modulePath);
                $alias = $manifest['alias'] ?? Str::kebab($name);
                $webRoutes = $this->extractEndpoints($routeBasePath.'web.php', false);
                $apiRoutes = $this->extractEndpoints($routeBasePath.'api.php', true);

                return [
                    'id' => Str::slug($name),
                    'name' => $name,
                    'alias' => $alias,
                    'description' => $this->resolveModuleDescription($name, $webRoutes, $apiRoutes),
                    'web_url' => $this->resolveModuleUrl($alias, $webRoutes),
                    'web_routes' => $webRoutes,
                    'api_routes' => $apiRoutes,
                    'supports_web' => ! empty($webRoutes),
                    'supports_api' => ! empty($apiRoutes),
                ];
            })
            ->sortBy('name')
            ->values();

        $modelGroups = collect(File::files(app_path('Models')))
            ->map(function (\SplFileInfo $file): array {
                $modelName = $file->getBasename('.php');

                return [
                    'name' => $modelName,
                    'group' => $this->resolveModelGroup($modelName),
                ];
            })
            ->groupBy('group')
            ->map(function ($models, string $group): array {
                return [
                    'id' => Str::slug($group),
                    'name' => $group,
                    'models' => $models->pluck('name')->sort()->values()->all(),
                    'count' => $models->count(),
                ];
            })
            ->sortBy('name')
            ->values();

        return [
            'stats' => [
                'module_count' => $modules->count(),
                'web_module_count' => $modules->where('supports_web', true)->count(),
                'api_endpoint_count' => $modules->sum(fn (array $module) => count($module['api_routes'])),
                'model_count' => $modelGroups->sum('count'),
            ],
            'modules' => $modules->all(),
            'model_groups' => $modelGroups->all(),
        ];
    }

    protected function extractEndpoints(string $routeFile, bool $api = false): array
    {
        if (! File::exists($routeFile)) {
            return [];
        }

        $contents = File::get($routeFile);
        preg_match("/Route::prefix\\('([^']+)'\\)/", $contents, $prefixMatch);
        $prefix = $prefixMatch[1] ?? '';
        $base = $api ? '/api' : '';

        preg_match_all("/Route::(get|post|put|patch|delete)\\('([^']+)'/", $contents, $matches, PREG_SET_ORDER);

        return collect($matches)
            ->map(function (array $match) use ($base, $prefix): array {
                $method = strtoupper($match[1]);
                $uri = $match[2];
                $path = collect([$base, $prefix, $uri === '/' ? null : $uri])
                    ->filter(fn ($segment) => $segment !== null && $segment !== '')
                    ->implode('/');

                return [
                    'method' => $method,
                    'path' => '/'.ltrim($path, '/'),
                ];
            })
            ->unique(fn (array $route) => $route['method'].' '.$route['path'])
            ->values()
            ->all();
    }

    protected function resolveModuleUrl(string $alias, array $webRoutes): ?string
    {
        if ($alias === 'admin') {
            return route('admin.dashboard');
        }

        if (empty($webRoutes)) {
            return null;
        }

        return url('/'.$alias);
    }

    protected function resolveModuleDescription(string $name, array $webRoutes, array $apiRoutes): string
    {
        return match (Str::lower($name)) {
            'admin' => 'Super admin authentication and control center for the FitPaxPro platform.',
            'users' => 'User-facing module with an existing web entry and authenticated API surface.',
            'gym' => 'Gym onboarding and authentication module with existing web and API endpoints.',
            default => 'Existing FitPaxPro module discovered from the current codebase.',
        };
    }

    protected function resolveModelGroup(string $modelName): string
    {
        return match (true) {
            Str::startsWith($modelName, ['User', 'Role', 'Permission']) => 'Access & Users',
            Str::startsWith($modelName, ['Gym', 'Attendance']) => 'Gym Operations',
            Str::startsWith($modelName, ['Diet', 'Exercise', 'Health', 'Recipe', 'Category']) => 'Fitness Planning',
            Str::startsWith($modelName, ['Blog', 'Forum', 'Page', 'Like']) => 'Content & Community',
            Str::startsWith($modelName, ['Media', 'Fcm', 'Notification']) => 'Media & Messaging',
            Str::startsWith($modelName, ['Admin', 'Session', 'Password', 'Failed', 'Job', 'Cache', 'Setting']) => 'Platform Operations',
            default => 'Core Domain',
        };
    }
}
