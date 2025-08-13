<?php

// Controller: app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    protected $adminDashboardService;

    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    public function dashboard()
    {
        $days = 30;
        $stats = $this->adminDashboardService->getAdminStats($days);

        return Inertia::render('admin-dashboard', [
            'stats' => $stats
        ]);
    }
}

// Service: app/Services/AdminDashboardService.php
