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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
