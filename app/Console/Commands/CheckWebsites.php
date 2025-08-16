<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Website;
use App\Jobs\CheckWebsiteJob;

class CheckWebsites extends Command
{
    protected $signature = 'websites:check';
    protected $description = 'Check all websites based on their interval';

    public function handle()
    {
        $websites = Website::where('is_active', true)->get();

        foreach ($websites as $website) {
            if (!$website->last_checked_at || 
                $website->last_checked_at->diffInSeconds(now()) >= $website->check_interval) 
            {
                dispatch(new CheckWebsiteJob($website));
                $this->info("Checking {$website->url}");
            }
        }

        return Command::SUCCESS;
    }
}
