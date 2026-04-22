<?php

namespace Modules\GYM\app\Interfaces;

interface GymMemberRepositoryInterface
{
    /**
     * Get paginated members for a specific gym.
     *
     * @param string $gymId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getMembersByGym(string $gymId, int $perPage = 15);

    /**
     * Add a new member to a gym and create their subscription.
     *
     * @param array $data
     * @return \App\Models\GymSubscription
     */
    public function addMember(array $data);

    /**
     * Get details of a specific gym member subscription.
     *
     * @param string $subscriptionId
     * @return \App\Models\GymSubscription
     */
    public function getMemberDetails(string $subscriptionId);
}
