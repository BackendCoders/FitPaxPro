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

    public function getAllPlans()
    {
        return \App\Models\GymFeePlan::with('gym')->latest()->get();
    }

    /**
     * Get all plans for a specific gym.
     * 
     * @param string $gymId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlansByGymId(string $gymId)
    {
        return \App\Models\GymFeePlan::where('gym_id', $gymId)->latest()->get();
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

    /**
     * 5-STEP PROVISIONING IMPLEMENTATIONS
     */

    public function createOperative(array $data)
    {
        return \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'] ?? \Str::random(16),
            'user_type' => 2, // Gym Owner
            'status' => 0
        ]);
    }

    public function verifyOtp(string $email, string $otp)
    {
        $verification = \DB::table('gym_otp_verifications')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->first();

        if (!$verification) return false;

        \DB::table('gym_otp_verifications')->where('id', $verification->id)->update(['is_used' => true]);
        
        $user = \App\Models\User::where('email', $email)->first();
        $user->update(['status' => 1, 'email_verified_at' => now()]);

        return $user;
    }

    public function initiateNode(\App\Models\User $owner, array $data)
    {
        return Gym::updateOrCreate(
            ['owner_id' => $owner->id, 'email' => $owner->email],
            [
                'name' => $data['gym_name'],
                'phone' => $owner->phone,
                'status' => 'pending'
            ]
        );
    }

    public function syncNodePlans(Gym $gym, ?array $templateIds, ?array $customPlans)
    {
        if ($templateIds) {
            $templates = \App\Models\MembershipPlanTemplate::whereIn('id', $templateIds)->get();
            foreach ($templates as $template) {
                \App\Models\GymFeePlan::create([
                    'gym_id' => $gym->id,
                    'name' => $template->name,
                    'tagline' => $template->tagline,
                    'description' => $template->description,
                    'features' => $template->features,
                    'price' => $template->price,
                    'is_active' => true,
                ]);
            }
        }

        if ($customPlans) {
            foreach ($customPlans as $cp) {
                \App\Models\GymFeePlan::create(array_merge($cp, ['gym_id' => $gym->id]));
            }
        }

        return $gym->load('feePlans');
    }

    public function uploadNodeAssets(Gym $gym, $mainImage, ?array $gallery)
    {
        if ($mainImage) {
            $gym->update(['image' => $mainImage->store('gyms', 'public')]);
        }

        if ($gallery) {
            foreach ($gallery as $file) {
                $path = $file->store('gyms/gallery', 'public');
                \App\Models\GymGalleryMedia::create([
                    'gym_id' => $gym->id,
                    'file_path' => $path,
                    'file_type' => 'image',
                    'file_size' => $file->getSize(),
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                ]);
            }
        }

        return $gym->load('galleryMedia');
    }
}
