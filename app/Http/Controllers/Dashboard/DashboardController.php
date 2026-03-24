<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware(['auth', 'permission:dashboard.view']);
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display Dashboard
     */
    public function index()
    {
        $stats = $this->dashboardService->getStats();

        return view('dashboard.index', compact('stats'));
    }
}