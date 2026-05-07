<?php

namespace Modules\Users\app\Repositories;

use App\Models\User;
use Modules\Users\app\Interfaces\UserProfileRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function getProfile(User $user): User
    {
        return $user->load('profile');
    }

    public function updateProfile(User $user, array $data): User
    {
        // Update basic user info if provided
        $userData = [];
        if (isset($data['name'])) $userData['name'] = $data['name'];
        if (isset($data['phone'])) $userData['phone'] = $data['phone'];
        
        if (isset($data['profile_image']) && $data['profile_image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $userData['profile_image'] = $data['profile_image']->store('avatars', 'public');
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        // Update profile specific info
        $profileFields = [
            'alternative_contact', 'gender', 'age', 'date_of_birth', 
            'current_weight', 'target_weight', 'body_fat_percentage', 
            'height', 'goal_type', 'workout_frequency_goal', 
            'activity_level', 'fitness_level', 'preferred_workout_time', 
            'blood_group', 'diet_type', 'medical_conditions', 
            'allergies', 'physical_limitations', 'is_public'
        ];

        $profileData = array_intersect_key($data, array_flip($profileFields));

        if (!empty($profileData)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return $user->load('profile');
    }

    public function getMeasurements(User $user)
    {
        return $user->healthLogs()
            ->orderBy('log_date', 'desc')
            ->get();
    }
}
