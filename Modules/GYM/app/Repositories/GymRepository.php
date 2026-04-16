<?php

namespace Modules\GYM\app\Repositories;

use App\Models\Gym;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;

class GymRepository implements GymRepositoryInterface
{
    /**
     * Get all gyms with their owners.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGyms()
    {
        return Gym::with('owner')->latest()->get();
    }

    /**
     * Get a gym by its UUID.
     * 
     * @param string $uuid
     * @return \App\Models\Gym|null
     */
    public function getGymById(string $uuid)
    {
        return Gym::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Create a new gym.
     * 
     * @param array $data
     * @return \App\Models\Gym
     */
    public function createGym(array $data)
    {
        $gym = Gym::create($data);
        
        if (isset($data['plans']) && is_array($data['plans'])) {
            foreach ($data['plans'] as $plan) {
                $gym->feePlans()->create($plan);
            }
        }
        
        return $gym;
    }

    /**
     * Update an existing gym.
     * 
     * @param string $uuid
     * @param array $data
     * @return \App\Models\Gym|null
     */
    public function updateGym(string $uuid, array $data)
    {
        $gym = $this->getGymById($uuid);
        if ($gym) {
            $gym->update($data);
            
            if (isset($data['plans']) && is_array($data['plans'])) {
                // For simplicity, we'll clear and recreate, or you could do a more complex sync
                $gym->feePlans()->delete();
                foreach ($data['plans'] as $plan) {
                    $gym->feePlans()->create($plan);
                }
            }
            
            return $gym;
        }
        return null;
    }

    /**
     * Delete a gym.
     * 
     * @param string $uuid
     * @return bool
     */
    public function deleteGym(string $uuid)
    {
        $gym = $this->getGymById($uuid);
        if ($gym) {
            return $gym->delete();
        }
        return false;
    }
}
