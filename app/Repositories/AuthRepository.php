<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Handle login request.
     * 
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials): bool
    {
        return Auth::attempt($credentials, $credentials['remember'] ?? false);
    }

    /**
     * Handle registration request.
     * 
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Handle logout request.
     * 
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
