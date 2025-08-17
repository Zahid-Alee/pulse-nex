<?php

namespace App\Console\Commands;

use App\Services\UptimeMonitorService;
use Illuminate\Console\Command;

class CheckUptimeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-uptime-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private UptimeMonitorService $uptimeService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting uptime checks...');

        $results = $this->uptimeService->checkAllWebsites();

        $this->info('Checked ' . count($results) . ' websites');

        return self::SUCCESS;
    }
}
