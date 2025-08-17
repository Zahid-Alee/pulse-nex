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

class CheckWebsiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Website $website;

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
        $result = $monitor->checkWebsite($this->website);

        UptimeCheck::create([
            'website_id'    => $this->website->id,
            'status'        => $result['status'],
            'response_time' => $result['response_time'],
            'status_code'   => $result['status_code'],
            'error_message' => $result['error_message'],
            'checked_at'    => $result['checked_at'],
        ]);
    }
}
