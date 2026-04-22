<?php

namespace Modules\GYM\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\GYM\app\Interfaces\GymRepositoryInterface;
use Modules\GYM\app\Repositories\GymRepository;

class GymRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GymRepositoryInterface::class, GymRepository::class);
        $this->app->bind(\Modules\GYM\app\Interfaces\GymMemberRepositoryInterface::class, \Modules\GYM\app\Repositories\GymMemberRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
