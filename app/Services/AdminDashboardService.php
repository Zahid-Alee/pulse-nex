<?php

namespace App\Services;

use App\Models\User;
use App\Models\Website;
use App\Models\UptimeCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    public function getAdminStats(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'overview' => $this->getOverviewStats($days),
            'users' => $this->getUserStats($days),
            'websites' => $this->getWebsiteStats($days),
            'performance' => $this->getPerformanceStats($days),
            'charts' => $this->getChartData($days),
        ];
    }

    protected function getOverviewStats(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'total_users' => User::count(),
            'total_websites' => Website::count(),
            'new_users_today' => User::whereDate('created_at', Carbon::today())->count(),
            'websites_up' => Website::where('status', 'up')->count(),
            'websites_down' => Website::where('status', 'down')->count(),
        ];
    }

    protected function getUserStats(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'recent_users' => User::with('websites')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                        'websites_count' => $user->websites()->count(),
                        'last_login' => $user->last_login_at,
                    ];
                }),
            'user_growth' => $this->getUserGrowthData($days),
            'top_users_by_websites' => User::withCount('websites')
                ->orderBy('websites_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'websites_count' => $user->websites_count,
                    ];
                }),
        ];
    }

    protected function getWebsiteStats(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'top_websites' => Website::with('user')
                ->withCount(['uptimeChecks as total_checks' => function ($query) use ($startDate) {
                    $query->where('checked_at', '>=', $startDate);
                }])
                ->orderBy('total_checks', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($website) use ($days) {
                    $checks = $website->recentChecks($days)->get();
                    $uptime = $checks->isEmpty() ? 0 :
                        round(($checks->where('status', 'up')->count() / $checks->count()) * 100, 2);

                    return [
                        'id' => $website->id,
                        'name' => $website->name,
                        'url' => $website->url,
                        'user_name' => $website->user->name,
                        'status' => $website->status,
                        'uptime_percentage' => $uptime,
                        'total_checks' => $website->total_checks,
                        'last_checked' => $website->last_checked_at,
                    ];
                }),
            'website_status_distribution' => [
                'up' => Website::where('status', 'up')->count(),
                'down' => Website::where('status', 'down')->count(),
                'unknown' => Website::whereNull('status')->count(),
            ],
            'recent_incidents' => UptimeCheck::with('website.user')
                ->where('status', 'down')
                ->where('checked_at', '>=', $startDate)
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($check) {
                    return [
                        'website_name' => $check->website->name,
                        'website_url' => $check->website->url,
                        'user_name' => $check->website->user->name,
                        'error_message' => $check->error_message,
                        'checked_at' => $check->checked_at,
                        'status_code' => $check->status_code,
                    ];
                }),
        ];
    }

    protected function getPerformanceStats(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        $totalChecks = UptimeCheck::where('checked_at', '>=', $startDate)->count();
        $upChecks = UptimeCheck::where('checked_at', '>=', $startDate)
            ->where('status', 'up')
            ->count();

        $avgResponseTime = UptimeCheck::where('checked_at', '>=', $startDate)
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->avg('response_time');

        return [
            'overall_uptime' => $totalChecks > 0 ? round(($upChecks / $totalChecks) * 100, 2) : 0,
            'average_response_time' => $avgResponseTime ? round($avgResponseTime, 2) : 0,
            'total_checks_performed' => $totalChecks,
            'slowest_websites' => Website::with('user')
                ->select('websites.*', DB::raw('AVG(uptime_checks.response_time) as avg_response_time'))
                ->join('uptime_checks', 'websites.id', '=', 'uptime_checks.website_id')
                ->where('uptime_checks.checked_at', '>=', $startDate)
                ->where('uptime_checks.status', 'up')
                ->whereNotNull('uptime_checks.response_time')
                ->groupBy('websites.id')
                ->orderBy('avg_response_time', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($website) {
                    return [
                        'name' => $website->name,
                        'url' => $website->url,
                        'user_name' => $website->user->name,
                        'avg_response_time' => round($website->avg_response_time, 2),
                    ];
                }),
        ];
    }

    protected function getChartData(int $days): array
    {
        return [
            'daily_uptime' => $this->getDailyUptimeData($days),
            'user_registrations' => $this->getDailyRegistrationData($days),
            'website_additions' => $this->getDailyWebsiteData($days),
            'response_time_trend' => $this->getResponseTimeTrend($days),
        ];
    }

    protected function getUserGrowthData(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    protected function getDailyUptimeData(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return UptimeCheck::selectRaw('DATE(checked_at) as date, 
                    COUNT(*) as total_checks,
                    SUM(CASE WHEN status = "up" THEN 1 ELSE 0 END) as up_checks')
            ->where('checked_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'uptime_percentage' => $item->total_checks > 0 ?
                        round(($item->up_checks / $item->total_checks) * 100, 2) : 0,
                ];
            })
            ->toArray();
    }

    protected function getDailyRegistrationData(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    protected function getDailyWebsiteData(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return Website::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    protected function getResponseTimeTrend(int $days): array
    {
        $startDate = Carbon::now()->subDays($days);

        return UptimeCheck::selectRaw('DATE(checked_at) as date, AVG(response_time) as avg_response_time')
            ->where('checked_at', '>=', $startDate)
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'avg_response_time' => round($item->avg_response_time, 2),
                ];
            })
            ->toArray();
    }
}
