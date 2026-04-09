<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminUserOperationsApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/user-operations/users",
     *     tags={"User Operations"},
     *     summary="List users",
     *     description="Returns paginated users for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string", example="john")),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="integer", enum={0,1}, example=1)),
     *     @OA\Parameter(name="user_type", in="query", required=false, @OA\Schema(type="integer", example=3)),
     *     @OA\Response(response=200, description="User list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        if (! Schema::hasTable('users')) {
            return response()->json([
                'success' => true,
                'message' => 'No users available.',
                'data' => ['users' => []],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => 0,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ]);
        }

        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));
        $query = User::query();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if (Schema::hasColumn('users', 'phone')) {
                    $builder->orWhere('phone', 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('status') && Schema::hasColumn('users', 'status')) {
            $query->where('status', (int) $request->query('status'));
        }

        if ($request->filled('user_type') && Schema::hasColumn('users', 'user_type')) {
            $query->where('user_type', (int) $request->query('user_type'));
        }

        if (Schema::hasColumn('users', 'created_at')) {
            $query->latest('created_at');
        }

        $results = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully.',
            'data' => [
                'users' => $results->items(),
            ],
            'meta' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/user-operations/users/{user}",
     *     tags={"User Operations"},
     *     summary="Show user",
     *     description="Returns a single user record.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="User fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        return response()->json([
            'success' => true,
            'message' => 'User fetched successfully.',
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/user-operations/users",
     *     tags={"User Operations"},
     *     summary="Create user",
     *     description="Creates a new user record.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="Password@123"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+919876543210"),
     *             @OA\Property(property="status", type="boolean", nullable=true, example=true),
     *             @OA\Property(property="user_type", type="integer", nullable=true, example=3)
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $payload = $request->validate($this->storeRules());

        if (array_key_exists('password', $payload)) {
            $payload['password'] = Hash::make($payload['password']);
        }

        if (Schema::hasColumn('users', 'status') && ! array_key_exists('status', $payload)) {
            $payload['status'] = true;
        }

        if (Schema::hasColumn('users', 'user_type') && ! array_key_exists('user_type', $payload)) {
            $payload['user_type'] = 3;
        }

        $user = User::query()->create($payload);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/user-operations/users/{user}",
     *     tags={"User Operations"},
     *     summary="Update user",
     *     description="Updates an existing user record.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Updated"),
     *             @OA\Property(property="email", type="string", format="email", example="john.updated@example.com"),
     *             @OA\Property(property="password", type="string", format="password", nullable=true, example="NewPass@123"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+919876543210"),
     *             @OA\Property(property="status", type="boolean", nullable=true, example=true),
     *             @OA\Property(property="user_type", type="integer", nullable=true, example=3)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function update(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $payload = $request->validate($this->updateRules($user));

        if (array_key_exists('password', $payload) && filled($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            unset($payload['password']);
        }

        $user->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => [
                'user' => $user->fresh(),
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/user-operations/users/{user}",
     *     tags={"User Operations"},
     *     summary="Delete user",
     *     description="Deletes a user record.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="User deleted"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/user-operations/users/{user}/activate",
     *     tags={"User Operations"},
     *     summary="Activate user",
     *     description="Sets user status to active.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="User activated"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function activate(Request $request, User $user): JsonResponse
    {
        return $this->setUserStatus($request, $user, true, 'User activated successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/admin/user-operations/users/{user}/deactivate",
     *     tags={"User Operations"},
     *     summary="Deactivate user",
     *     description="Sets user status to inactive.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="string", example="1")),
     *     @OA\Response(response=200, description="User deactivated"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function deactivate(Request $request, User $user): JsonResponse
    {
        return $this->setUserStatus($request, $user, false, 'User deactivated successfully.');
    }

    protected function setUserStatus(Request $request, User $user, bool $status, string $message): JsonResponse
    {
        if ($response = $this->ensureAdmin($request)) {
            return $response;
        }

        if (! Schema::hasColumn('users', 'status')) {
            return response()->json([
                'success' => false,
                'message' => 'Status column is not available on users.',
            ], 422);
        }

        $user->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'user' => $user->fresh(),
            ],
        ]);
    }

    protected function storeRules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $rules['phone'] = ['nullable', 'string', 'max:255', Rule::unique('users', 'phone')];
        }

        if (Schema::hasColumn('users', 'status')) {
            $rules['status'] = ['nullable', 'boolean'];
        }

        if (Schema::hasColumn('users', 'user_type')) {
            $rules['user_type'] = ['nullable', 'integer'];
        }

        return $rules;
    }

    protected function updateRules(User $user): array
    {
        $rules = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $rules['phone'] = ['nullable', 'string', 'max:255', Rule::unique('users', 'phone')->ignore($user->id)];
        }

        if (Schema::hasColumn('users', 'status')) {
            $rules['status'] = ['nullable', 'boolean'];
        }

        if (Schema::hasColumn('users', 'user_type')) {
            $rules['user_type'] = ['nullable', 'integer'];
        }

        return $rules;
    }

    protected function ensureAdmin(Request $request): ?JsonResponse
    {
        $admin = $request->user();

        if (! $admin || ! $this->isAdmin($admin)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can access this endpoint.',
            ], 403);
        }

        return null;
    }

    protected function isAdmin(User $user): bool
    {
        if ((int) $user->user_type === 0) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super-admin', 'super admin']);
    }
}
