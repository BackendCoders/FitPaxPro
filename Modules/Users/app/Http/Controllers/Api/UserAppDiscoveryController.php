<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Gym;
use App\Models\User;
use OpenApi\Annotations as OA;

class UserAppDiscoveryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user-app/banners",
     *     tags={"User App: Gym Discovery"},
     *     summary="Get Promotional Banners",
     *     description="Retrieve dynamic promotional banners to display at the top of the app.",
     *     @OA\Response(response=200, description="List of banners")
     * )
     */
    public function banners(): JsonResponse
    {
        $banners = \App\Models\Banner::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/categories",
     *     tags={"User App: Gym Discovery"},
     *     summary="Get gym and workout categories",
     *     description="Retrieve category names, icons, and IDs for filtering.",
     *     @OA\Response(response=200, description="List of categories")
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->select('id', 'name', 'slug', 'icon_class')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * @OA\Get(
     *     path="/user-app/search",
     *     tags={"User App: Gym Discovery"},
     *     summary="Global Search (Gyms, Categories, Trainers)",
     *     description="Search across gym names, categories, and trainers. Returns formatted results grouped by type.",
     *     @OA\Parameter(name="query", in="query", required=true, @OA\Schema(type="string"), description="Search text"),
     *     @OA\Parameter(name="lat", in="query", required=false, @OA\Schema(type="number"), description="User latitude"),
     *     @OA\Parameter(name="lng", in="query", required=false, @OA\Schema(type="number"), description="User longitude"),
     *     @OA\Response(response=200, description="Formatted search results")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $queryText = $request->input('query');
        
        if (!$queryText) {
            return response()->json([
                'success' => true,
                'data' => [
                    'gyms' => [],
                    'categories' => [],
                    'trainers' => []
                ]
            ]);
        }

        // Search Categories
        $categories = Category::where('is_active', true)
            ->where('name', 'like', "%{$queryText}%")
            ->select('id', 'name', 'slug', 'icon_class')
            ->limit(5)
            ->get();

        // Search Gyms
        $gymQuery = Gym::where('status', 'active')
            ->where(function($q) use ($queryText) {
                $q->where('name', 'like', "%{$queryText}%")
                  ->orWhere('brand_name', 'like', "%{$queryText}%");
            })
            ->with(['galleryMedia' => function ($q) {
                $q->where('file_type', 'image')->limit(1);
            }]);

        if ($request->has('lat') && $request->has('lng')) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $gymQuery->selectRaw("*, ( 6371 * acos( cos( radians(?) ) *
                cos( radians( latitude ) ) *
                cos( radians( longitude ) - radians(?) ) +
                sin( radians(?) ) *
                sin( radians( latitude ) ) )
            ) AS distance", [$lat, $lng, $lat])
            ->orderBy('distance', 'asc');
        } else {
            $gymQuery->orderBy('rating_avg', 'desc');
        }

        $gyms = $gymQuery->limit(10)->get();

        // Search Trainers (Assuming user_type = 2 is coach/trainer)
        $trainers = User::where('user_type', 2)
            ->where('name', 'like', "%{$queryText}%")
            ->select('id', 'name', 'profile_image')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'gyms' => $gyms,
                'trainers' => $trainers
            ]
        ]);
    }
}
