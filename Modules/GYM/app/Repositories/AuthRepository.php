<?php

namespace Modules\GYM\app\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\GYM\app\Contracts\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'user_type' => $data['user_type'] ?? 3,
            'status' => $data['status'] ?? 1,
        ]);

        $user->loadMissing('profile');
        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return [
            'user' => $this->formatUser($user),
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
        $identifier = $data['email'] ?? $data['phone'] ?? null;

        $query = User::query();

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = $query->where('email', $identifier)->first();
        } else {
            $user = $query->where('phone', $identifier)->first();
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['The provided credentials are invalid.'],
            ]);
        }

        if (! (bool) $user->status) {
            throw ValidationException::withMessages([
                'identifier' => ['Your account is inactive.'],
            ]);
        }

        $user->loadMissing('profile');
        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return [
            'user' => $this->formatUser($user),
            'token' => $token,
        ];
    }

    public function sendOtp(string $emailOrPhone): string
    {
        $user = User::where('email', $emailOrPhone)->orWhere('phone', $emailOrPhone)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No operative found with this identity.'],
            ]);
        }

        $otp = rand(100000, 999999);
        \DB::table('gym_otp_verifications')->insert([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $otp;
    }

    public function verifyLoginOtp(string $emailOrPhone, string $otp): array
    {
        $user = User::where('email', $emailOrPhone)->orWhere('phone', $emailOrPhone)->first();
        
        if (!$user) {
            throw ValidationException::withMessages(['otp' => ['Identity signal failure.']]);
        }

        $verification = \DB::table('gym_otp_verifications')
            ->where('email', $user->email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->first();

        if (!$verification) {
            throw ValidationException::withMessages(['otp' => ['Invalid or expired verification signal.']]);
        }

        \DB::table('gym_otp_verifications')->where('id', $verification->id)->update(['is_used' => true]);

        $user->update(['status' => 1]); // Ensure user is active upon OTP verification
        
        $token = $user->createToken('mobile-app')->plainTextToken;

        return [
            'user' => $this->formatUser($user),
            'token' => $token,
        ];
    }

    public function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_image' => $user->profile_image,
            'status' => (bool) $user->status,
            'user_type' => (int) $user->user_type,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'profile' => $user->relationLoaded('profile') && $user->profile
                ? $user->profile->toArray()
                : null,
        ];
    }
}
