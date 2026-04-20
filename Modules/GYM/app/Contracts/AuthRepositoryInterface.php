<?php

namespace Modules\GYM\app\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function register(array $data): array;

    public function login(array $data): array;

    public function sendOtp(string $emailOrPhone): string;

    public function verifyLoginOtp(string $emailOrPhone, string $otp): array;

    public function formatUser(User $user): array;
}
