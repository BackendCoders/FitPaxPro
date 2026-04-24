<?php

namespace Modules\Users\app\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Gym;

interface GymListingRepositoryInterface
{
    /**
     * Get a paginated list of public, active gyms.
     * Can optionally filter by search query or city.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getActiveGyms(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get details of a specific gym by its ID or Slug.
     *
     * @param string $identifier ID or Slug
     * @return Gym|null
     */
    public function getGymDetails(string $identifier): ?Gym;
}
