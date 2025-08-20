<?php

namespace App\Console\Commands;

use App\Models\Website;
use App\Jobs\CheckWebsiteJob;
use App\Services\UptimeMonitorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckUptimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-uptime-command 
                            {--sync : Run checks synchronously instead of using queues}
                            {--limit=50 : Maximum number of websites to check per run}
                            {--website-id= : Check specific website by ID}
                            {--debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check uptime for all websites that are due for checking';

    /**
     * Execute the console command.
     */
    public function handle(UptimeMonitorService $monitor)
    {
        $startTime = microtime(true);
        $this->info("Starting uptime checks at " . Carbon::now()->toDateTimeString());

        try {
            // Show debug info if requested
            if ($this->option('debug')) {
                $this->showDebugInfo();
                return 0;
            }

            // Check specific website if ID provided
            if ($websiteId = $this->option('website-id')) {
                return $this->checkSpecificWebsite($websiteId, $monitor);
            }

            // Get websites that need checking
            $limit = (int) $this->option('limit');
            $websites = Website::needsCheck()
                              ->limit($limit)
                              ->get();

            if ($websites->isEmpty()) {
                $this->info("No websites need checking at this time.");
                $this->showDebugInfo();
                return 0;
            }

            $this->info("Found {$websites->count()} websites that need checking.");

            $processedCount = 0;
            $errorCount = 0;

            foreach ($websites as $website) {
                try {
                    $this->line("Processing website: {$website->name} ({$website->url})");
                    $this->line("  Last checked: " . ($website->last_checked_at ? $website->last_checked_at->toDateTimeString() : 'Never'));
                    $this->line("  Interval: {$website->check_interval} seconds");
                    
                    if ($this->option('sync')) {
                        // Run synchronously for debugging
                        $result = $monitor->checkWebsite($website);
                        $this->info("  Status: {$result['status']} | Response Time: {$result['response_time']}ms | Code: {$result['status_code']}");
                    } else {
                        // Dispatch job to queue
                        CheckWebsiteJob::dispatch($website);
                        $this->info("  Job dispatched to queue");
                    }

                    $processedCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("  Error: " . $e->getMessage());
                    Log::error("Command failed to process website", [
                        'website_id' => $website->id,
                        'website_url' => $website->url,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->info("\n=== Uptime Check Summary ===");
            $this->info("Total websites processed: {$processedCount}");
            $this->info("Errors encountered: {$errorCount}");
            $this->info("Execution time: {$executionTime} seconds");
            $this->info("Mode: " . ($this->option('sync') ? 'Synchronous' : 'Queue'));

            Log::info("Uptime check command completed", [
                'processed_count' => $processedCount,
                'error_count' => $errorCount,
                'execution_time' => $executionTime,
                'mode' => $this->option('sync') ? 'sync' : 'queue'
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Command failed with exception: " . $e->getMessage());
            Log::error("Uptime check command failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Check a specific website by ID
     */
    protected function checkSpecificWebsite(int $websiteId, UptimeMonitorService $monitor): int
    {
        try {
            $website = Website::findOrFail($websiteId);
            
            $this->info("Checking specific website: {$website->name} ({$website->url})");
            $this->info("Last checked: " . ($website->last_checked_at ? $website->last_checked_at->toDateTimeString() : 'Never'));
            $this->info("Check interval: {$website->check_interval} seconds");
            
            if (!$website->isDueForCheck()) {
                $nextCheck = $website->next_check_time;
                $this->warn("Website is not due for check yet. Next check scheduled for: {$nextCheck->toDateTimeString()}");
                $secondsUntilNext = $website->seconds_until_next_check;
                $this->warn("Seconds until next check: {$secondsUntilNext}");
                
                if (!$this->confirm('Do you want to force the check anyway?')) {
                    return 0;
                }
            }

            $startTime = microtime(true);
            $result = $monitor->checkWebsite($website);
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $this->info("\n=== Check Results ===");
            $this->info("Status: {$result['status']}");
            $this->info("Response Time: {$result['response_time']}ms");
            $this->info("Status Code: {$result['status_code']}");
            $this->info("Error Message: " . ($result['error_message'] ?? 'None'));
            $this->info("Execution Time: {$executionTime} seconds");

            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to check website: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Show debug information about websites and their check schedules
     */
    public function showDebugInfo(): void
    {
        $this->info("\n=== Debug Information ===");
        
        $allWebsites = Website::orderBy('id')->get();
        $needsCheck = Website::needsCheck()->get();
        
        $this->info("Total websites: {$allWebsites->count()}");
        $this->info("Websites needing check: {$needsCheck->count()}");
        
        if ($allWebsites->isEmpty()) {
            $this->warn("No websites found in database.");
            return;
        }
        
        $this->table(
            ['ID', 'Name', 'URL', 'Interval (sec)', 'Last Checked', 'Next Check', 'Due?'],
            $allWebsites->map(function ($website) {
                $isDue = $website->isDueForCheck();
                $nextCheckTime = $website->next_check_time;
                
                return [
                    $website->id,
                    $website->name,
                    $website->url,
                    $website->check_interval,
                    $website->last_checked_at ? $website->last_checked_at->format('Y-m-d H:i:s') : 'Never',
                    $nextCheckTime ? $nextCheckTime->format('Y-m-d H:i:s') : 'Now',
                    $isDue ? 'Yes' : 'No'
                ];
            })->toArray()
        );
    }
}