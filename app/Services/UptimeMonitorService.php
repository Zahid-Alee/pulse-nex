<?php

namespace App\Services;

use App\Models\Website;
use App\Models\UptimeCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\WebsiteDownEmail;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\returnSelf;

class UptimeMonitorService
{
    public function checkAllWebsites(): array
    {
        $results = [];
        $websites = Website::needsCheck()->get();

        Log::info("Starting website check. Total websites: " . $websites->count());

        foreach ($websites as $website) {
            // Check if enough time has passed since last check
            if (!$this->shouldCheckWebsite($website)) {
                Log::info("Skipping website check - interval not reached", [
                    'website_id' => $website->id,
                    'last_checked_at' => $website->last_checked_at,
                    'check_interval' => $website->check_interval,
                    'next_check_time' => $this->getNextCheckTime($website)
                ]);
                continue;
            }

            Log::info("Preparing to check website", [
                'website_id' => $website->id,
                'url' => $website->url ?? null,
                'check_interval' => $website->check_interval,
                'current_time' => now()->toDateTimeString(),
                'last_checked_at' => $website->last_checked_at
            ]);

            try {
                $result = $this->checkWebsite($website);
                Log::info("Website checked successfully", [
                    'website_id' => $website->id,
                    'status' => $result['status'] ?? 'unknown',
                    'response_time' => $result['response_time'] ?? null,
                    'status_code' => $result['status_code'] ?? null
                ]);
                $results[] = $result;
            } catch (\Exception $e) {
                Log::error("Error checking website", [
                    'website_id' => $website->id,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $results[] = [
                    'website_id' => $website->id,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        Log::info("Finished website checks");
        return $results;
    }

    /**
     * Check if enough time has passed since the last check
     * Now uses app timezone instead of hardcoded UTC
     */
    protected function shouldCheckWebsite(Website $website): bool
    {
        // If never checked before, check now
        if (!$website->last_checked_at) {
            return true;
        }

        // Use app configured timezone instead of hardcoded UTC
        $appTimezone = config('app.timezone');
        $now = Carbon::now($appTimezone);
        $lastChecked = Carbon::parse($website->last_checked_at)->setTimezone($appTimezone);

        // Calculate time difference in seconds
        $timeDifferenceSeconds = $now->diffInSeconds($lastChecked);

        // Check interval is already in seconds
        $intervalSeconds = $website->check_interval;

        Log::debug("Check interval calculation", [
            'website_id' => $website->id,
            'app_timezone' => $appTimezone,
            'now_local' => $now->toDateTimeString(),
            'last_checked_local' => $lastChecked->toDateTimeString(),
            'time_difference_seconds' => $timeDifferenceSeconds,
            'interval_seconds' => $intervalSeconds,
            'should_check' => $timeDifferenceSeconds >= $intervalSeconds
        ]);

        return $timeDifferenceSeconds >= $intervalSeconds;
    }

    /**
     * Get the next scheduled check time for debugging
     * Now uses app timezone
     */
    protected function getNextCheckTime(Website $website): ?string
    {
        if (!$website->last_checked_at) {
            return 'Now (never checked)';
        }

        $appTimezone = config('app.timezone');
        $lastChecked = Carbon::parse($website->last_checked_at)->setTimezone($appTimezone);
        $nextCheck = $lastChecked->addSeconds($website->check_interval);

        return $nextCheck->toDateTimeString() . ' (' . $appTimezone . ')';
    }

    public function checkWebsite(Website $website): array
    {
        $startTime = microtime(true);
        $appTimezone = config('app.timezone');
        $checkedAt = Carbon::now($appTimezone);

        // Default values
        $status = 'unknown';
        $responseTime = null;
        $statusCode = null;
        $errorMessage = null;

        Log::info("===== Starting website check =====", [
            'website_id' => $website->id,
            'url' => $website->url,
            'timeout' => $website->timeout,
            'is_active' => $website->is_active,
            'checked_at' => $checkedAt->toDateTimeString(),
            'timezone' => $appTimezone,
        ]);


        if (!$website->is_active) {
            Log::warning("Website monitoring inactive, skipping check", [
                'website_id' => $website->id,
            ]);
            return [
                'website_id' => $website->id,
                'status' => 'inactive',
                'message' => 'Website monitoring is inactive.'
            ];
        }

        try {
            Log::info("Sending HTTP request...", [
                'website_id' => $website->id,
                'url' => $website->url,
            ]);

            $response = Http::timeout($website->timeout ?? 30)
                ->connectTimeout(10)
                ->withHeaders(['User-Agent' => 'UptimeMonitor/1.0'])
                ->withOptions(['verify' => false, 'allow_redirects' => true])
                ->get($website->url);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $statusCode = $response->status();

            Log::info("HTTP response received", [
                'website_id' => $website->id,
                'status_code' => $statusCode,
                'response_time_ms' => $responseTime,
                'successful' => $response->successful(),
            ]);

            if ($response->successful() || ($statusCode >= 300 && $statusCode < 400)) {
                $status = 'up';
                Log::info("Website is UP", [
                    'website_id' => $website->id,
                    'status_code' => $statusCode,
                ]);
            } else {
                $status = 'down';
                $errorMessage = "HTTP {$statusCode}";
                Log::warning("Website is DOWN (HTTP error)", [
                    'website_id' => $website->id,
                    'status_code' => $statusCode,
                    'error_message' => $errorMessage,
                ]);
                $this->handleDowntimeNotification($website, $errorMessage);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $status = 'down';
            $errorMessage = 'Connection timeout/refused';

            Log::error("Website DOWN - ConnectionException", [
                'website_id' => $website->id,
                'error_message' => $errorMessage,
                'exception' => $e->getMessage(),
            ]);
            $this->handleDowntimeNotification($website, $errorMessage);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $status = 'down';
            $errorMessage = 'Request failed: ' . $e->getMessage();

            Log::error("Website DOWN - RequestException", [
                'website_id' => $website->id,
                'error_message' => $errorMessage,
            ]);
            $this->handleDowntimeNotification($website, $errorMessage);
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $status = 'down';
            $errorMessage = 'Unknown error: ' . $e->getMessage();

            Log::error("Website DOWN - General Exception", [
                'website_id' => $website->id,
                'error_message' => $errorMessage,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->handleDowntimeNotification($website, $errorMessage);
        }

        // 🚨 Update website record
        try {
            $website->update([
                'status' => $status,
                'last_checked_at' => $checkedAt,
            ]);

            Log::info("Website record updated", [
                'website_id' => $website->id,
                'status' => $status,
                'last_checked_at' => $checkedAt->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update website record", [
                'website_id' => $website->id,
                'exception' => $e->getMessage(),
            ]);
        }

        // 🚨 Create uptime check record
        try {
            Log::info("Creating uptime check record...", [
                'website_id' => $website->id,
                'status' => $status,
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'error_message' => $errorMessage,
                'checked_at' => $checkedAt->toDateTimeString(),
            ]);

            $uptimeCheck = UptimeCheck::create([
                'website_id' => $website->id,
                'status' => $status,
                'response_time' => $responseTime,
                'status_code' => $statusCode,
                'error_message' => $errorMessage,
                'checked_at' => $checkedAt,
            ]);

            Log::info("Uptime check created successfully", [
                'uptime_check_id' => $uptimeCheck->id ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create uptime check record", [
                'website_id' => $website->id,
                'exception' => $e->getMessage(),
            ]);
        }

        Log::info("===== Website check completed =====", [
            'website_id' => $website->id,
            'final_status' => $status,
            'response_time_ms' => $responseTime,
            'status_code' => $statusCode,
        ]);

        return [
            'website_id' => $website->id,
            'website_name' => $website->name,
            'website_url' => $website->url,
            'status' => $status,
            'response_time' => $responseTime,
            'status_code' => $statusCode,
            'error_message' => $errorMessage,
            'checked_at' => $checkedAt->toISOString(),
        ];
    }


    /**
     * Handle downtime notifications
     */
    protected function handleDowntimeNotification(Website $website, string $errorMessage): void
    {
        try {
            // Only send email if website was previously up or this is the first down check
            $lastCheck = UptimeCheck::where('website_id', $website->id)
                ->latest('checked_at')
                ->first();

            if (!$lastCheck || $lastCheck->status === 'up') {
                Log::info("Sending downtime notification", [
                    'website_id' => $website->id,
                    'user_email' => $website->user->email ?? 'N/A'
                ]);

                if ($website->user && $website->user->email) {
                    Mail::to($website->user->email)
                        ->send(new WebsiteDownEmail($website, $errorMessage));
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed to send downtime notification", [
                'website_id' => $website->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function cleanupOldChecks(Website $website): void
    {
        try {
            $appTimezone = config('app.timezone');
            $cutoffDate = Carbon::now($appTimezone)->subDays(90);

            $deletedCount = UptimeCheck::where('website_id', $website->id)
                ->where('checked_at', '<', $cutoffDate)
                ->delete();

            if ($deletedCount > 0) {
                Log::info("Cleaned up old uptime checks", [
                    'website_id' => $website->id,
                    'deleted_count' => $deletedCount,
                    'cutoff_date' => $cutoffDate->toDateTimeString()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to cleanup old checks", [
                'website_id' => $website->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function calculateIncidents($checks): int
    {
        $incidents = 0;
        $inDowntime = false;

        foreach ($checks->sortBy('checked_at') as $check) {
            if ($check->status === 'down' && !$inDowntime) {
                $incidents++;
                $inDowntime = true;
            } elseif ($check->status === 'up') {
                $inDowntime = false;
            }
        }

        return $incidents;
    }

    protected function calculateTotalDowntime($checks, int $checkInterval): int
    {
        $downChecks = $checks->where('status', 'down')->count();
        return round(($downChecks * $checkInterval) / 60); // Convert to minutes
    }

    // Helper to reduce chart points
    protected function reduceChartPoints($data, $maxPoints = 15)
    {
        $count = $data->count();
        if ($count <= $maxPoints) {
            return $data->values();
        }

        $step = max(1, floor($count / $maxPoints));
        return $data->values()->filter(function ($item, $index) use ($step) {
            return $index % $step === 0;
        })->values();
    }

    public function getWebsiteStats(Website $website, int $days = 30): array
    {
        $checks = $website->recentChecks($days)->get();

        if ($checks->isEmpty()) {
            return [
                'total_checks' => 0,
                'uptime_percentage' => null,
                'average_response_time' => null,
                'total_downtime_minutes' => 0,
                'incidents' => 0,
                'hourly_data' => []
            ];
        }

        $totalChecks = $checks->count();
        $upChecks = $checks->where('status', 'up')->count();
        $uptimePercentage = round(($upChecks / $totalChecks) * 100, 2);

        $averageResponseTime = $checks
            ->where('status', 'up')
            ->whereNotNull('response_time')
            ->avg('response_time');

        $incidents = $this->calculateIncidents($checks);
        $totalDowntime = $this->calculateTotalDowntime($checks, $website->check_interval);

        // Group by hour using app timezone
        $appTimezone = config('app.timezone');
        $hourlyData = $checks->groupBy(function ($check) use ($appTimezone) {
            return Carbon::parse($check->checked_at)->setTimezone($appTimezone)->format('Y-m-d H:00');
        })->map(function ($group, $hour) {
            $total = $group->count();
            $up = $group->where('status', 'up')->count();
            return [
                'hour' => $hour,
                'uptime_percentage' => round(($up / $total) * 100, 2)
            ];
        })->sortKeys()->values();

        // Reduce chart points
        $hourlyData = $this->reduceChartPoints($hourlyData);

        return [
            'total_checks' => $totalChecks,
            'uptime_percentage' => $uptimePercentage,
            'average_response_time' => $averageResponseTime ? round($averageResponseTime, 2) : null,
            'total_downtime_minutes' => $totalDowntime,
            'incidents' => $incidents,
            'hourly_data' => $hourlyData
        ];
    }

    public function getAllWebsitesStats($websites, int $days = 30): array
    {
        if ($websites->isEmpty()) {
            return [
                'total_websites' => 0,
                'uptime_percentage' => null,
                'average_response_time' => null,
                'total_downtime_minutes' => 0,
                'incidents' => 0,
                'hourly_data' => []
            ];
        }

        $totalChecks = 0;
        $upChecks = 0;
        $totalResponseTime = 0;
        $totalDowntime = 0;
        $totalIncidents = 0;
        $hourlyData = collect();
        $appTimezone = config('app.timezone');

        foreach ($websites as $website) {
            $checks = $website->recentChecks($days)->get();

            if ($checks->isEmpty()) {
                continue;
            }

            $totalChecks += $checks->count();
            $upChecks += $checks->where('status', 'up')->count();
            $totalResponseTime += $checks->where('status', 'up')->whereNotNull('response_time')->avg('response_time') ?? 0;
            $totalDowntime += $this->calculateTotalDowntime($checks, $website->check_interval);
            $totalIncidents += $this->calculateIncidents($checks);

            $checks->groupBy(function ($check) use ($appTimezone) {
                return Carbon::parse($check->checked_at)->setTimezone($appTimezone)->format('Y-m-d H:00');
            })->each(function ($group, $hour) use (&$hourlyData) {
                $total = $group->count();
                $up = $group->where('status', 'up')->count();
                $uptimePct = round(($up / $total) * 100, 2);

                $hourlyData->push([
                    'hour' => $hour,
                    'uptime_percentage' => $uptimePct
                ]);
            });
        }

        $uptimePercentage = $totalChecks > 0 ? round(($upChecks / $totalChecks) * 100, 2) : null;
        $avgResponseTime = $totalChecks > 0 ? round($totalResponseTime / $websites->count(), 2) : null;

        // Reduce chart points
        $hourlyData = $this->reduceChartPoints($hourlyData->sortBy('hour')->values());

        return [
            'total_websites' => $websites->count(),
            'uptime_percentage' => $uptimePercentage,
            'average_response_time' => $avgResponseTime,
            'total_downtime_minutes' => $totalDowntime,
            'incidents' => $totalIncidents,
            'hourly_data' => $hourlyData
        ];
    }
}
