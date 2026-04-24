<?php

namespace Modules\Users\app\Interfaces;

use App\Models\User;

interface UserAppRepositoryInterface
{
    /**
     * Create or retrieve a user based on identity details.
     *
     * @param array $data
     * @return User
     */
    public function createIdentity(array $data): User;

    /**
     * Verify the OTP and return the user if valid.
     *
     * @param string $email
     * @param string $otp
     * @return User|null
     */
    public function verifyOtp(string $email, string $otp): ?User;

    /**
     * Update user physical attributes (Step 2).
     *
     * @param User $user
     * @param array $physicalData
     * @return User
     */
    public function updatePhysical(User $user, array $physicalData): User;

    /**
     * Update user goals and lifestyle (Step 3).
     *
     * @param User $user
     * @param array $goalsData
     * @return User
     */
    public function updateGoals(User $user, array $goalsData): User;

    /**
     * Update user medical information (Step 4).
     *
     * @param User $user
     * @param array $medicalData
     * @return User
     */
    public function updateMedical(User $user, array $medicalData): User;

    /**
     * Upload user avatar and final preferences (Step 5).
     *
     * @param User $user
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param bool|null $isPublic
     * @return User
     */
    public function uploadAvatar(User $user, $image, ?bool $isPublic): User;

    /**
     * Log historical body measurements.
     *
     * @param User $user
     * @param array $measurementData
     * @return \App\Models\UserBodyMeasurement
     */
    public function logBodyMeasurement(User $user, array $measurementData);
}
