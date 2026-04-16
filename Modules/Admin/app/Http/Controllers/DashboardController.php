<?php

namespace Modules\Admin\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\app\Interfaces\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    private $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Display the dashboard.
     */
    public function index()
    {
        $stats = $this->dashboardRepository->getStats();
        $recentUsers = $this->dashboardRepository->getRecentActivity();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
