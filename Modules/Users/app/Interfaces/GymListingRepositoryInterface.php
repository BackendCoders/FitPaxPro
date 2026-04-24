<?php

namespace Modules\Users\app\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Gym;

interface GymListingRepositoryInterface
{
    /**
     * Get a paginated list of public, active gyms.
     * Can optionally filter by search query, city, and location (lat, lng, radius).
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActiveGyms(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get top-rated / featured gyms.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedGyms(int $limit = 5);

    /**
     * Get details of a specific gym by its ID or Slug.
     *
     * @param string $identifier ID or Slug
     * @return Gym|null
     */
    public function getGymDetails(string $identifier): ?Gym;

    /**
     * Get pricing plans for a specific gym by its ID or Slug.
     *
     * @param string $identifier ID or Slug
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGymPlans(string $identifier);
}
