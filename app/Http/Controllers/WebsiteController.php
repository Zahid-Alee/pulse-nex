<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Website;
use App\Services\UptimeMonitorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    protected UptimeMonitorService $uptimeMonitor;

    public function __construct(UptimeMonitorService $uptimeMonitor)
    {
        $this->uptimeMonitor = $uptimeMonitor;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $websites = $user->websites()->get();

        $days = 30;
        $stats = $this->uptimeMonitor->getAllWebsitesStats($websites, $days);

        if ($user->is_admin == 1) {
            return redirect()->route('admin.dashboard')->with('success', 'Website added successfully');
        }

        return Inertia::render('dashboard', [
            'websites' => $websites,
            'stats' => $stats
        ]);
    }

    public function view()
    {
        $websites = Website::where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($website) {
                return [
                    'id' => $website->id,
                    'name' => $website->name,
                    'url' => $website->url,
                    'status' => $website->status,
                    'check_interval' => $website->check_interval,
                    'last_checked_at' => $website->last_checked_at
                        ? $website->last_checked_at->format('Y-m-d H:i:s')
                        : 'Never',
                    'timeout' => $website->timeout,
                    'is_active' => $website->is_active,
                ];
            });

        return Inertia::render('websites/list', [
            'websites' => $websites
        ]);
    }

    public function createView()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)->first();

        return Inertia::render('websites/create', [
            'subscription' => $subscription,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : [],
        ]);
    }


    public function editView(Website $website)
    {
        $this->authorize('update', $website);

        return Inertia::render('websites/edit', [
            'website' => $website
        ]);
    }


    public function showView(Website $website)
    {
        // $this->authorize('view', $website);

        $days = 30;
        $stats = $this->uptimeMonitor->getWebsiteStats($website, $days);

        // Get recent checks with proper pagination
        $history = $website->recentChecks($days)->paginate(10);

        return Inertia::render('websites/view', [
            'website' => $website,
            'stats' => $stats,
            'history' => $history
        ]);
    }

    public function history(Request $request, Website $website): JsonResponse
    {
        $this->authorize('view', $website);

        $days = $request->get('days', 7);
        $perPage = min($request->get('per_page', 50), 100);

        // Use the fixed recentChecks method
        $checks = $website->recentChecks($days)->paginate($perPage);

        // Transform the data to ensure consistent timezone display
        $checks->getCollection()->transform(function ($check) {
            // The timezone conversion should already be handled by the model
            // but we can add additional formatting here if needed
            $check->formatted_checked_at = $check->checked_at->format('Y-m-d H:i:s T');
            return $check;
        });

        return response()->json([
            'success' => true,
            'data' => $checks
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $websites = Website::where('user_id', $request->user()->id)
            ->with(['uptimeChecks' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('name')
            ->get();

        $websites = $websites->map(function ($website) {
            return [
                'id' => $website->id,
                'name' => $website->name,
                'url' => $website->url,
                'status' => $website->status,
                'last_checked_at' => $website->last_checked_at,
                'is_active' => $website->is_active,
                'check_interval' => $website->check_interval,
                'uptime_percentage' => $website->uptime_percentage,
                'average_response_time' => $website->average_response_time,
                'current_downtime' => $website->current_downtime,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $websites
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription;

        if (!$subscription) {
            return back()->withErrors(['subscription' => 'No active subscription found.']);
        }

        $currentWebsites = $user->websites()->count();
        if ($subscription->monitors_limit && $currentWebsites >= $subscription->monitors_limit) {
            return back()->withErrors(['monitors_limit' => 'You have reached your plan\'s website limit.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'check_interval' => 'integer',
            'timeout' => 'integer|min:5|max:120',
        ]);

        if ($subscription->check_interval && $validated['check_interval'] < $subscription->check_interval) {
            return back()->withErrors(['check_interval' => 'Check interval cannot be lower than your plan\'s minimum limit of ' . $subscription->check_interval . ' seconds.']);
        }

        $validated['user_id'] = $user->id;

        $website = Website::create($validated);
        $this->uptimeMonitor->checkWebsite($website);

        return redirect()->route('website.list')->with('success', 'Website added successfully');
    }

    public function update(Request $request, Website $website)
    {
        $this->authorize('update', $website);
        $subscription = $request->user()->subscription;

        $validated = $request->validate([
            'name' => 'string|max:255',
            'url' => 'url|max:500',
            'check_interval' => 'integer|min:60|max:3600',
            'timeout' => 'integer|min:5|max:120',
            'is_active' => 'boolean',
        ]);

        if ($subscription && isset($validated['check_interval']) && $validated['check_interval'] < $subscription->check_interval) {
            return back()->withErrors(['check_interval' => 'Check interval cannot be lower than your plan\'s minimum limit of ' . $subscription->check_interval . ' seconds.']);
        }

        $website->update($validated);

        return redirect()->route('website.edit', $website->id)->with('success', 'Website updated successfully');
    }


    public function destroy(Website $website)
    {
        $this->authorize('delete', $website);

        $website->delete();

        $websites = Website::latest()->get();

        return redirect()
            ->route('website.list')
            ->with([
                'success' => 'Website deleted successfully',
                'websites' => $websites
            ]);
    }


    public function show(Request $request, Website $website): JsonResponse
    {
        $this->authorize('view', $website);

        $days = $request->get('days', 30);
        $stats = $this->uptimeMonitor->getWebsiteStats($website, $days);

        return response()->json([
            'success' => true,
            'data' => [
                'website' => $website,
                'stats' => $stats
            ]
        ]);
    }


    public function stats(Request $request, Website $website): JsonResponse
    {
        $this->authorize('view', $website);

        $days = $request->get('days', 30);
        $stats = $this->uptimeMonitor->getWebsiteStats($website, $days);

        // Get hourly uptime data for chart
        $hourlyStats = $this->getHourlyStats($website, $days);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'hourly_data' => $hourlyStats
            ]
        ]);
    }

    protected function getHourlyStats(Website $website, int $days): array
    {
        $startDate = now()->subDays($days);

        $checks = $website->uptimeChecks()
            ->where('checked_at', '>=', $startDate)
            ->orderBy('checked_at')
            ->get()
            ->groupBy(function ($check) {
                return $check->checked_at->format('Y-m-d H');
            });

        $hourlyStats = [];

        foreach ($checks as $hour => $hourChecks) {
            $totalChecks = $hourChecks->count();
            $upChecks = $hourChecks->where('status', 'up')->count();
            $uptime = $totalChecks > 0 ? round(($upChecks / $totalChecks) * 100, 2) : 0;
            $avgResponseTime = $hourChecks->where('status', 'up')
                ->whereNotNull('response_time')
                ->avg('response_time');

            $hourlyStats[] = [
                'hour' => $hour . ':00',
                'uptime_percentage' => $uptime,
                'average_response_time' => $avgResponseTime ? round($avgResponseTime, 2) : null,
                'total_checks' => $totalChecks
            ];
        }

        return $hourlyStats;
    }
}
