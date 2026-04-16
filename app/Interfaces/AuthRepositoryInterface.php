<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    /**
     * Handle login request.
     * 
     * @param array $credentials
     * @return bool
     */
    public function login(array $credentials): bool;

    /**
     * Handle registration request.
     * 
     * @param array $data
     * @return \App\Models\User
     */
    public function register(array $data);

    /**
     * Handle logout request.
     * 
     * @return void
     */
    public function logout(): void;
}
