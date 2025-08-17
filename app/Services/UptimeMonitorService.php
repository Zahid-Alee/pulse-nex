<?php

namespace App\Services;

use App\Models\Website;
use App\Models\UptimeCheck;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\WebsiteDownEmail;
use Illuminate\Support\Facades\Mail;




class UptimeMonitorService
{
    public function checkAllWebsites(): array
    {
        $results = [];
        $websites = Website::needsCheck()->get();

        Log::info("Starting website check. Total websites: " . $websites->count());

        foreach ($websites as $website) {
            Log::info("Preparing to check website", [
                'website_id'     => $website->id,
                'url'            => $website->url ?? null, // if you have url column
                'check_interval' => $website->check_interval,
                'current_time'   => now()->toDateTimeString(),
            ]);

            try {
                $result = $this->checkWebsite($website);
                Log::info("Website checked successfully", [
                    'website_id' => $website->id,
                    'status'     => $result['status'] ?? 'unknown',
                ]);
                $results[] = $result;
            } catch (\Exception $e) {
                Log::error("Error checking website", [
                    'website_id' => $website->id,
                    'message'    => $e->getMessage(),
                    'trace'      => $e->getTraceAsString(),
                ]);
                $results[] = [
                    'website_id' => $website->id,
                    'status'     => 'error',
                    'message'    => $e->getMessage()
                ];
            }
        }

        Log::info("Finished website checks");

        return $results;
    }


    public function checkWebsite(Website $website): array
    {
        $startTime = microtime(true);
        $checkedAt = Carbon::now();
        $status = 'down';
        $responseTime = null;
        $statusCode = null;
        $errorMessage = null;

        try {
            $response = Http::timeout($website->timeout)
                ->connectTimeout(10)
                ->get($website->url);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000);
            $statusCode = $response->status();

            if ($response->successful()) {
                $status = 'up';
            } else {
                $status = 'down';
                $errorMessage = "HTTP {$statusCode}";

                // Mail::to($website->user->email)
                //     ->send(new WebsiteDownEmail($website, $errorMessage));
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $errorMessage = 'Connection timeout or refused';
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $errorMessage = 'Request failed: ' . $e->getMessage();
        } catch (\Exception $e) {
            $errorMessage = 'Unknown error: ' . $e->getMessage();
        }

        $website->update([
            'status' => $status,
            'last_checked_at' => $checkedAt
        ]);

        $this->cleanupOldChecks($website);

        $uptimeCheck = UptimeCheck::create([
            'website_id'    => $website->id,
            'status'        => $status,
            'response_time' => $responseTime,
            'status_code'   => $statusCode,
            'error_message' => $errorMessage,
            'checked_at'    => $checkedAt,
        ]);

        return [
            'website_id' => $website->id,
            'website_name' => $website->name,
            'website_url' => $website->url,
            'status' => $status,
            'response_time' => $responseTime,
            'status_code' => $statusCode,
            'error_message' => $errorMessage,
            'checked_at' => $checkedAt->toISOString()
        ];
    }

    protected function cleanupOldChecks(Website $website): void
    {
        $cutoffDate = Carbon::now()->subDays(90);

        UptimeCheck::where('website_id', $website->id)
            ->where('checked_at', '<', $cutoffDate)
            ->delete();
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

        // group by hour
        $hourlyData = $checks->groupBy(function ($check) {
            return \Carbon\Carbon::parse($check->checked_at)->format('Y-m-d H:00');
        })->map(function ($group, $hour) {
            $total = $group->count();
            $up = $group->where('status', 'up')->count();
            return [
                'hour' => $hour,
                'uptime_percentage' => round(($up / $total) * 100, 2)
            ];
        })->sortKeys()->values();

        // ✅ Reduce chart points
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

            $checks->groupBy(function ($check) {
                return \Carbon\Carbon::parse($check->checked_at)->format('Y-m-d H:00');
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

        // ✅ Reduce chart points
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
