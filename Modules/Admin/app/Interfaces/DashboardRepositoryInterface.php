<?php

namespace Modules\Admin\app\Interfaces;

interface DashboardRepositoryInterface
{
    /**
     * Get statistics for the dashboard.
     * 
     * @return array
     */
    public function getStats(): array;

    /**
     * Get recent activities/members.
     * 
     * @return array
     */
    public function getRecentActivity(): array;
}
