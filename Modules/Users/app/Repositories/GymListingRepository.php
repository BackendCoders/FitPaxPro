<?php

namespace Modules\Users\app\Repositories;

use App\Models\Gym;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Users\app\Interfaces\GymListingRepositoryInterface;

class GymListingRepository implements GymListingRepositoryInterface
{
    public function getActiveGyms(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Gym::where('status', 'active')
            ->with(['galleryMedia' => function ($q) {
                $q->where('file_type', 'image')->limit(1);
            }]);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['lat']) && !empty($filters['lng'])) {
            $lat = (float) $filters['lat'];
            $lng = (float) $filters['lng'];
            $radius = !empty($filters['radius']) ? (int) $filters['radius'] : 10; // Default 10km

            // Haversine formula
            $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) *
                cos( radians( latitude ) ) *
                cos( radians( longitude ) - radians(?) ) +
                sin( radians(?) ) *
                sin( radians( latitude ) ) )
            ) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (empty($filters['lat']) || empty($filters['lng'])) {
            // Only apply these orderings if we are not sorting by distance
            $query->orderBy('is_sponsored', 'desc')
                  ->orderBy('rating_avg', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getFeaturedGyms(int $limit = 5)
    {
        return Gym::where('status', 'active')
            ->with(['galleryMedia' => function ($q) {
                $q->where('file_type', 'image')->limit(1);
            }])
            ->orderBy('rating_avg', 'desc')
            ->orderBy('is_sponsored', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getGymDetails(string $identifier): ?Gym
    {
        return Gym::where('status', 'active')
            ->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('slug', $identifier);
            })
            ->with([
                'feePlans' => function($q) {
                    $q->where('is_active', true);
                },
                'galleryMedia',
                'reviews' => function($q) {
                    $q->where('status', 'published')->latest()->take(5);
                },
                'reviews.user' => function($q) {
                    $q->select('id', 'name', 'profile_image');
                }
            ])
            ->first();
    }

    public function getGymPlans(string $identifier)
    {
        $gym = Gym::where('status', 'active')
            ->where(function ($q) use ($identifier) {
                $q->where('id', $identifier)
                  ->orWhere('slug', $identifier);
            })
            ->first();

        if (!$gym) {
            return collect();
        }

        return \App\Models\GymFeePlan::where('gym_id', $gym->id)
            ->where('is_active', true)
            ->get();
    }
}
