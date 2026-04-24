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

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        // Sponsor gyms first, then highest rated
        $query->orderBy('is_sponsored', 'desc')
              ->orderBy('rating_avg', 'desc')
              ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
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
}
