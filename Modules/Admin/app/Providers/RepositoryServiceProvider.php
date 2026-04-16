<?php

namespace Modules\Admin\app\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Admin\app\Interfaces\DashboardRepositoryInterface;
use Modules\Admin\app\Repositories\DashboardRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(
            \Modules\Admin\app\Interfaces\SettingRepositoryInterface::class,
            \Modules\Admin\app\Repositories\SettingRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
