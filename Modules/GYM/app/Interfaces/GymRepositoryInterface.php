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

    /**
     * Get all gym subscriptions.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSubscriptions();

    /**
     * Get a subscription by its ID.
     * 
     * @param string $id
     * @return \App\Models\GymSubscription|null
     */
    public function getSubscriptionById(string $id);

    /**
     * Get all membership plans.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlans();

    /**
     * Get a plan by its ID.
     * 
     * @param string $id
     * @return \App\Models\GymFeePlan|null
     */
    public function getPlanById(string $id);

    /**
     * Create a new plan.
     * 
     * @param array $data
     * @return \App\Models\GymFeePlan
     */
    public function createPlan(array $data);

    /**
     * Update an existing plan.
     * 
     * @param string $id
     * @param array $data
     * @return \App\Models\GymFeePlan|null
     */
    public function updatePlan(string $id, array $data);

    /**
     * Delete a plan.
     * 
     * @param string $id
     * @return bool
     */
    public function deletePlan(string $id);

    /**
     * 5-STEP PROVISIONING METHODS
     */
    public function createOperative(array $data);
    public function verifyOtp(string $email, string $otp);
    public function initiateNode(\App\Models\User $owner, array $data);
    public function syncNodePlans(\App\Models\Gym $gym, ?array $templateIds, ?array $customPlans);
    public function uploadNodeAssets(\App\Models\Gym $gym, $mainImage, ?array $gallery);
}
