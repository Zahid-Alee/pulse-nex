<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UptimeMonitorService;

class CheckWebsites extends Command
{
    protected $signature = 'websites:check';
    protected $description = 'Check websites uptime based on their intervals';

    protected UptimeMonitorService $monitor;

    public function __construct(UptimeMonitorService $monitor)
    {
        parent::__construct();
        $this->monitor = $monitor;
    }

    public function handle()
    {
        $results = $this->monitor->checkAllWebsites();

        foreach ($results as $result) {
            if ($result['status'] === 'error') {
                $this->error("Website ID {$result['website_id']} error: {$result['message']}");
            } else {
                $this->info("Checked website {$result['website_name']} ({$result['website_url']}): {$result['status']}");
            }
        }
        return 0;
    }
}
