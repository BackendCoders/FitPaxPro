<?php

namespace Modules\Users\app\Interfaces;

use App\Models\User;

interface UserProfileRepositoryInterface
{
    /**
     * Get the full profile data for a user.
     *
     * @param User $user
     * @return User
     */
    public function getProfile(User $user): User;

    /**
     * Update the user's profile information.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User;

    /**
     * Get the user's body measurements history.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMeasurements(User $user);
}
