<?php

namespace Modules\GYM\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GymGalleryMedia;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class GymVideoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/gym/videos",
     *     summary="Get all gym YouTube videos with filters",
     *     tags={"Discovery"},
     *     @OA\Parameter(name="search", in="query", description="Search by video title or gym name", @OA\Schema(type="string")),
     *     @OA\Parameter(name="gym_id", in="query", description="Filter by gym UUID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="city", in="query", description="Filter by city", @OA\Schema(type="string")),
     *     @OA\Parameter(name="category_id", in="query", description="Filter by category UUID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_main_video", in="query", description="Filter by main video status (0 or 1)", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = GymGalleryMedia::where('file_type', 'youtube')
            ->where('status', 'approved')
            ->with(['gym' => function ($q) {
                $q->select('id', 'name', 'city', 'address', 'image', 'status');
            }])
            ->withCount(['likes', 'comments']);

        // Filter by specific Gym
        if ($request->filled('gym_id')) {
            $query->where('gym_id', $request->gym_id);
        }

        // Filter by Main Video Status
        if ($request->has('is_main_video')) {
            $query->where('is_main_video', $request->boolean('is_main_video'));
        }

        // Search by video title or gym name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhereHas('gym', function (Builder $gq) use ($search) {
                      $gq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by City (through Gym)
        if ($request->filled('city')) {
            $city = $request->city;
            $query->whereHas('gym', function (Builder $q) use ($city) {
                $q->where('city', 'like', "%{$city}%");
            });
        }

        // Filter by Category (through Gym)
        if ($request->filled('category_id')) {
            $categoryId = $request->category_id;
            $query->whereHas('gym.categories', function (Builder $q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        // Ensure we only show videos for active gyms
        $query->whereHas('gym', function (Builder $q) {
            $q->where('status', 'active');
        });

        $videos = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'YouTube videos retrieved successfully.',
            'data' => $videos->getCollection()->map(function ($video) {
                return [
                    'id' => $video->id,
                    'gym_id' => $video->gym_id,
                    'gym_name' => $video->gym->name ?? 'N/A',
                    'video_title' => $video->file_name,
                    'video_url' => $video->file_path,
                    'mime_type' => $video->mime_type,
                    'is_main_video' => (bool)$video->is_main_video,
                    'likes_count' => $video->likes_count,
                    'comments_count' => $video->comments_count,
                    'is_liked' => auth('sanctum')->check() 
                        ? $video->likes()->where('user_id', auth('sanctum')->id())->exists() 
                        : false,
                    'gym_details' => [
                        'city' => $video->gym->city ?? null,
                        'image' => $video->gym->image_url ?? null,
                        'status' => $video->gym->status ?? null,
                    ],
                    'created_at' => $video->created_at->toDateTimeString(),
                ];
            }),
            'pagination' => [
                'total' => $videos->total(),
                'per_page' => $videos->perPage(),
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
            ]
        ]);
    }
}
