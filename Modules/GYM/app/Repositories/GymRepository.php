<?php

namespace Modules\GYM\app\Repositories;

use App\Models\Gym;
use App\Models\GymGalleryMedia;
use Illuminate\Http\UploadedFile;
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
    public function createGym(array $data, ?UploadedFile $image = null, array $gallery = [], array $youtubeLinks = [])
    {
        $gym = Gym::create($data);
        
        if (isset($data['plans']) && is_array($data['plans'])) {
            foreach ($data['plans'] as $plan) {
                $gym->feePlans()->create($plan);
            }
        }

        $this->syncMedia($gym, $image, $gallery, $youtubeLinks);
        
        return $gym;
    }

    /**
     * Update an existing gym.
     * 
     * @param string $uuid
     * @param array $data
     * @return \App\Models\Gym|null
     */
    public function updateGym(string $uuid, array $data, ?UploadedFile $image = null, array $gallery = [], array $youtubeLinks = [])
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

            $this->syncMedia($gym, $image, $gallery, $youtubeLinks);
            
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

    public function uploadNodeAssets(Gym $gym, $mainImage, ?array $gallery, array $youtubeLinks = [])
    {
        $this->syncMedia($gym, $mainImage, $gallery ?? [], $youtubeLinks);

        return $gym->load(['galleryMedia', 'videoMedia']);
    }

    /**
     * Sync gym media assets including main image, gallery and YouTube videos.
     */
    protected function syncMedia(Gym $gym, ?UploadedFile $image = null, array $gallery = [], array $youtubeLinks = []): void
    {
        if ($image) {
            $gym->update(['image' => $image->store('gyms', 'public')]);
        }

        foreach ($gallery as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('gyms/gallery', 'public');
            GymGalleryMedia::create([
                'gym_id' => $gym->id,
                'file_path' => $path,
                'file_type' => 'image',
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
            ]);
        }

        if (! empty($youtubeLinks)) {
            $gym->galleryMedia()->where('file_type', 'youtube')->delete();

            foreach ($youtubeLinks as $link) {
                $url = is_array($link) ? ($link['url'] ?? $link['link'] ?? null) : $link;

                if (! $url) {
                    continue;
                }

                GymGalleryMedia::create([
                    'gym_id' => $gym->id,
                    'file_path' => $url,
                    'file_type' => 'youtube',
                    'file_name' => is_array($link) ? ($link['title'] ?? 'YouTube Video') : 'YouTube Video',
                    'mime_type' => 'text/url',
                    'is_main_video' => false,
                ]);
            }
        }
    }

    public function getDashboardSummary(string $gymId)
    {
        // Mocked Implementation based on payload requirement
        return [
            "stats" => [
                "active_members" => 142,
                "check_ins_today" => 32,
                "monthly_revenue" => 45000.00,
                "pending_enquiries" => 5
            ],
            "recent_activity" => [
                [ "time" => "2 mins ago", "message" => "Aman Verma checked in", "type" => "attendance" ],
                [ "time" => "1 hr ago", "message" => "New subscription: Elite Plan", "type" => "payment" ]
            ],
            "attendance_trend" => [12, 45, 67, 32, 88, 54, 32]
        ];
    }

    public function checkInMember(array $data)
    {
        // Mocked Implementation based on payload requirement
        return [
            "attendance_id" => \Str::uuid()->toString(),
            "check_in_time" => now()->format('Y-m-d H:i:s'),
            "member_name" => "John Doe"
        ];
    }

    public function getProvisionSections()
    {
        return [
            [ "id" => "attendance", "title" => "Attendance", "icon" => "fact_check", "route" => "/attendance", "color" => "#FCA5A5" ],
            [ "id" => "members", "title" => "Members", "icon" => "people", "route" => "/members", "color" => "#7DD3FC" ],
            [ "id" => "classes", "title" => "Classes", "icon" => "fitness_center", "route" => "/classes", "color" => "#5EEAD4" ],
            [ "id" => "reports", "title" => "Reports", "icon" => "analytics", "route" => "/reports", "color" => "#B794F4" ]
        ];
    }

    public function getDietPlans()
    {
        return [
            "plan_name" => "Muscle Bulk 3000",
            "calories_target" => 3000,
            "macros" => [ "protein" => "180g", "carbs" => "350g", "fats" => "80g" ],
            "meals" => [
                [ "time" => "Breakfast", "food" => "6 Egg Whites, 100g Oats", "calories" => 550 ],
                [ "time" => "Post-Workout", "food" => "Whey Protein, 1 Banana", "calories" => 300 ]
            ]
        ];
    }

    public function getExercisePlans()
    {
        return [
            "title" => "5-Day Hypertrophy Split",
            "difficulty" => "Advanced",
            "schedule" => [
                [
                    "day" => "Monday",
                    "muscle_group" => "Chest & Triceps",
                    "exercises" => [
                        [ "name" => "Incline Bench Press", "sets" => 4, "reps" => "8-10", "rest" => "90s" ],
                        [ "name" => "Cable Flyes", "sets" => 3, "reps" => "15", "rest" => "60s" ]
                    ]
                ]
            ]
        ];
    }

    public function storeEnquiry(array $data)
    {
        return true;
    }

    public function storeReview(array $data)
    {
        return true;
    }

    public function registerFcmToken(array $data)
    {
        return true;
    }

    public function getRevenueReport(string $gymId, string $period)
    {
        return [
            "total_revenue" => 125000.00,
            "labels" => ["Jan", "Feb", "Mar", "Apr"],
            "values" => [25000, 30000, 28000, 42000]
        ];
    }
}
