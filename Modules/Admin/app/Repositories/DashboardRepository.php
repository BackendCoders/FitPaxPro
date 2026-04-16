<?php

namespace Modules\Admin\app\Repositories;

use App\Models\User;
use App\Models\Gym;
use App\Models\AttendanceLog;
use Modules\Admin\app\Interfaces\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get statistics for the dashboard.
     * 
     * @return array
     */
    public function getStats(): array
    {
        return [
            'total_members' => User::count(),
            'total_gyms' => Gym::count(),
            'active_sessions' => AttendanceLog::whereDate('created_at', today())->count(),
            'new_signups' => User::whereDate('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Get recent activities/members.
     * 
     * @return array
     */
    public function getRecentActivity(): array
    {
        return User::latest()->take(5)->get()->toArray();
    }
}
