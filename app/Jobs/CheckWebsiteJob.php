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
use Carbon\Carbon;

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
        Log::info("🔄 CheckWebsiteJob started", [
            'website_id' => $this->website->id,
            'website_url' => $this->website->url,
            'job_attempt' => $this->attempts(),
            'queued_at' => now()->toDateTimeString(),
        ]);

        try {
            // Always check the website - the command already filters for websites that need checking
            // But we can add a safety check here
            $this->website->refresh();
            
            if (!$this->website->isDueForCheck()) {
                Log::info("⏩ Skipping website check - not due yet", [
                    'website_id' => $this->website->id,
                    'last_checked_at' => $this->website->last_checked_at?->toDateTimeString(),
                    'check_interval_sec' => $this->website->check_interval,
                    'next_check_time' => $this->website->next_check_time?->toDateTimeString(),
                ]);
                return;
            }

            Log::info("🚀 Performing website check", [
                'website_id' => $this->website->id,
                'last_checked_at' => $this->website->last_checked_at?->toDateTimeString(),
                'check_interval_sec' => $this->website->check_interval,
            ]);

            $result = $monitor->checkWebsite($this->website);

            Log::info("✅ Website check completed", [
                'website_id' => $this->website->id,
                'status' => $result['status'],
                'response_time_ms' => $result['response_time'],
                'status_code' => $result['status_code'],
                'checked_at' => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Website check failed", [
                'website_id' => $this->website->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
            ]);

            if ($this->attempts() >= $this->tries) {
                $this->createFailedCheck($e->getMessage());
            }

            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Create a failed check record when all job attempts are exhausted
     */
    protected function createFailedCheck(string $errorMessage): void
    {
        try {
            $checkedAt = now();

            $this->website->update([
                'status' => 'down',
                'last_checked_at' => $checkedAt
            ]);

            UptimeCheck::create([
                'website_id' => $this->website->id,
                'status' => 'down',
                'response_time' => null,
                'status_code' => null,
                'error_message' => 'Job failed after ' . $this->tries . ' attempts: ' . $errorMessage,
                'checked_at' => $checkedAt,
            ]);

            Log::warning("⚠️ Website marked as DOWN after all retries failed", [
                'website_id' => $this->website->id,
                'error_message' => $errorMessage,
                'checked_at' => $checkedAt->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            Log::critical("🚨 Failed to record website failure", [
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
        Log::alert("💥 CheckWebsiteJob permanently failed", [
            'website_id' => $this->website->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'failed_at' => now()->toDateTimeString(),
        ]);
    }
}