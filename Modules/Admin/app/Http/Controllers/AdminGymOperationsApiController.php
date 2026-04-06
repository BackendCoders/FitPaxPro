<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Gym;
use App\Models\GymEnquiry;
use App\Models\GymFeePlan;
use App\Models\GymGalleryMedia;
use App\Models\GymReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;

class AdminGymOperationsApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/gyms",
     *     tags={"Gym Operations"},
     *     summary="List gyms",
     *     description="Returns paginated gym records for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Gym list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function gyms(Request $request): JsonResponse
    {
        return $this->listResource($request, new Gym(), 'gyms');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/attendance",
     *     tags={"Gym Operations"},
     *     summary="List attendance logs",
     *     description="Returns paginated attendance logs for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Attendance list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function attendance(Request $request): JsonResponse
    {
        return $this->listResource($request, new AttendanceLog(), 'attendance_logs');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/enquiries",
     *     tags={"Gym Operations"},
     *     summary="List gym enquiries",
     *     description="Returns paginated gym enquiries for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Enquiry list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function enquiries(Request $request): JsonResponse
    {
        return $this->listResource($request, new GymEnquiry(), 'gym_enquiries');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/fee-plans",
     *     tags={"Gym Operations"},
     *     summary="List gym fee plans",
     *     description="Returns paginated gym fee plans for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Fee plan list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function feePlans(Request $request): JsonResponse
    {
        return $this->listResource($request, new GymFeePlan(), 'gym_fee_plans');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/gallery-media",
     *     tags={"Gym Operations"},
     *     summary="List gym gallery media",
     *     description="Returns paginated gym gallery media records for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Gallery media list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function galleryMedia(Request $request): JsonResponse
    {
        return $this->listResource($request, new GymGalleryMedia(), 'gym_gallery_media');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/gym-operations/reviews",
     *     tags={"Gym Operations"},
     *     summary="List gym reviews",
     *     description="Returns paginated gym reviews for admin operations.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", example=10)),
     *     @OA\Response(response=200, description="Review list fetched"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden: non-admin token")
     * )
     */
    public function reviews(Request $request): JsonResponse
    {
        return $this->listResource($request, new GymReview(), 'gym_reviews');
    }

    protected function listResource(Request $request, Model $model, string $key): JsonResponse
    {
        $admin = $request->user();

        if (! $admin || ! $this->isAdmin($admin)) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin users can access this endpoint.',
            ], 403);
        }

        if (! Schema::hasTable($model->getTable())) {
            return response()->json([
                'success' => true,
                'message' => 'No records available.',
                'data' => [$key => []],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => 0,
                    'total' => 0,
                    'last_page' => 1,
                ],
            ]);
        }

        $perPage = max(1, min(100, (int) $request->query('per_page', 10)));

        $query = $model->newQuery();

        if (Schema::hasColumn($model->getTable(), 'created_at')) {
            $query->latest('created_at');
        }

        $results = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $key)).' fetched successfully.',
            'data' => [
                $key => $results->items(),
            ],
            'meta' => [
                'current_page' => $results->currentPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'last_page' => $results->lastPage(),
            ],
        ]);
    }

    protected function isAdmin(User $user): bool
    {
        if ((int) $user->user_type === 0) {
            return true;
        }

        return $user->hasAnyRole(['admin', 'super-admin', 'super admin']);
    }
}
