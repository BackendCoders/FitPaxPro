<?php

namespace Modules\GYM\app\Interfaces;

interface GymRepositoryInterface
{
    /**
     * Get all gyms.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGyms();

    /**
     * Get a gym by its UUID.
     * 
     * @param string $uuid
     * @return \App\Models\Gym|null
     */
    public function getGymById(string $uuid);

    /**
     * Create a new gym.
     * 
     * @param array $data
     * @return \App\Models\Gym
     */
    public function createGym(array $data);

    /**
     * Update an existing gym.
     * 
     * @param string $uuid
     * @param array $data
     * @return \App\Models\Gym|null
     */
    public function updateGym(string $uuid, array $data);

    /**
     * Delete a gym.
     * 
     * @param string $uuid
     * @return bool
     */
    public function deleteGym(string $uuid);
}
