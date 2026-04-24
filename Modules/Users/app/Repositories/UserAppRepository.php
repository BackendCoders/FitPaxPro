<?php

namespace Modules\Users\app\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Users\app\Interfaces\UserAppRepositoryInterface;

class UserAppRepository implements UserAppRepositoryInterface
{
    public function createIdentity(array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'user_type' => 1, // assuming 1 is regular user
                'status' => false,
            ]
        );
    }

    public function verifyOtp(string $email, string $otp): ?User
    {
        $record = DB::table('gym_otp_verifications')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($record) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // optional: mark user as verified
                // $user->update(['email_verified_at' => now()]);
            }
            return $user;
        }

        return null;
    }

    public function updatePhysical(User $user, array $physicalData): User
    {
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $physicalData
        );
        return $user;
    }

    public function updateGoals(User $user, array $goalsData): User
    {
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $goalsData
        );
        return $user;
    }

    public function updateMedical(User $user, array $medicalData): User
    {
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $medicalData
        );
        return $user;
    }

    public function uploadAvatar(User $user, $image, ?bool $isPublic): User
    {
        if ($image) {
            $path = $image->store('avatars', 'public');
            $user->update(['profile_image' => $path]);
        }
        
        if ($isPublic !== null) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['is_public' => $isPublic]
            );
        }

        // Finalize registration
        $user->update(['status' => true]);

        return $user;
    }

    public function logBodyMeasurement(User $user, array $measurementData)
    {
        return \App\Models\UserBodyMeasurement::create(array_merge(
            ['user_id' => $user->id, 'recorded_at' => now()],
            $measurementData
        ));
    }
}
