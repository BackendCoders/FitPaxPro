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
    public function getGymById(string $id)
    {
        return Gym::where('id', $id)->firstOrFail();
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

    /**
     * Get all gym subscriptions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSubscriptions()
    {
        return \App\Models\GymSubscription::with(['gym', 'user', 'plan'])->latest()->get();
    }

    /**
     * Get a subscription by its ID.
     * 
     * @param string $id
     * @return \App\Models\GymSubscription|null
     */
    public function getSubscriptionById(string $id)
    {
        return \App\Models\GymSubscription::where('id', $id)->firstOrFail();
    }

    /**
     * Get all membership plans.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlans()
    {
        return \App\Models\GymFeePlan::with('gym')->latest()->get();
    }

    /**
     * Get a plan by its ID.
     * 
     * @param string $id
     * @return \App\Models\GymFeePlan|null
     */
    public function getPlanById(string $id)
    {
        return \App\Models\GymFeePlan::where('id', $id)->firstOrFail();
    }

    /**
     * Create a new plan.
     * 
     * @param array $data
     * @return \App\Models\GymFeePlan
     */
    public function createPlan(array $data)
    {
        return \App\Models\GymFeePlan::create($data);
    }

    /**
     * Update an existing plan.
     * 
     * @param string $id
     * @param array $data
     * @return \App\Models\GymFeePlan|null
     */
    public function updatePlan(string $id, array $data)
    {
        $plan = $this->getPlanById($id);
        if ($plan) {
            $plan->update($data);
            return $plan;
        }
        return null;
    }

    /**
     * Delete a plan.
     * 
     * @param string $id
     * @return bool
     */
    public function deletePlan(string $id)
    {
        $plan = $this->getPlanById($id);
        if ($plan) {
            return $plan->delete();
        }
        return false;
    }
}
