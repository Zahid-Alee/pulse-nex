<?php

namespace App\Jobs;

use App\Models\Website;
use App\Models\UptimeCheck;
use App\Services\UptimeMonitorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Website $website;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * Execute the job.
     */
    public function handle(UptimeMonitorService $monitor)
    {
        try {
            Log::info("Starting CheckWebsiteJob", [
                'website_id' => $this->website->id,
                'website_url' => $this->website->url,
                'job_attempt' => $this->attempts()
            ]);

            // Check if the website should be checked based on interval
            if (!$this->shouldCheckWebsite()) {
                Log::info("Skipping website check - interval not reached", [
                    'website_id' => $this->website->id,
                    'last_checked_at' => $this->website->last_checked_at,
                    'check_interval' => $this->website->check_interval
                ]);
                return;
            }

            $result = $monitor->checkWebsite($this->website);

            Log::info("CheckWebsiteJob completed successfully", [
                'website_id' => $this->website->id,
                'status' => $result['status'],
                'response_time' => $result['response_time'],
                'status_code' => $result['status_code']
            ]);

        } catch (\Exception $e) {
            Log::error("CheckWebsiteJob failed", [
                'website_id' => $this->website->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'trace' => $e->getTraceAsString()
            ]);

            // If this is the final attempt, create a failed check record
            if ($this->attempts() >= $this->tries) {
                $this->createFailedCheck($e->getMessage());
            }

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Check if enough time has passed since the last check
     */
    protected function shouldCheckWebsite(): bool
    {
        // Refresh the model to get the latest data
        $this->website->refresh();

        // If never checked before, check now
        if (!$this->website->last_checked_at) {
            return true;
        }

        // Get current time in UTC to ensure consistency
        $now = \Carbon\Carbon::now('UTC');
        $lastChecked = \Carbon\Carbon::parse($this->website->last_checked_at)->utc();
        
        // Calculate time difference in seconds
        $timeDifferenceSeconds = $now->diffInSeconds($lastChecked);
        
        // Convert check_interval from minutes to seconds
        $intervalSeconds = $this->website->check_interval * 60;
        
        return $timeDifferenceSeconds >= $intervalSeconds;
    }

    /**
     * Create a failed check record when all job attempts are exhausted
     */
    protected function createFailedCheck(string $errorMessage): void
    {
        try {
            $checkedAt = \Carbon\Carbon::now('UTC');

            // Update website status
            $this->website->update([
                'status' => 'down',
                'last_checked_at' => $checkedAt
            ]);

            // Create uptime check record
            UptimeCheck::create([
                'website_id' => $this->website->id,
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => 'Job failed after ' . $this->tries . ' attempts: ' . $errorMessage,
                'checked_at' => $checkedAt,
            ]);

            Log::info("Created failed check record", [
                'website_id' => $this->website->id,
                'error_message' => $errorMessage
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to create failed check record", [
                'website_id' => $this->website->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("CheckWebsiteJob permanently failed", [
            'website_id' => $this->website->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}