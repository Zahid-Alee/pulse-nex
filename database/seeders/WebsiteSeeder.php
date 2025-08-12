<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\UptimeCheck;
use Carbon\Carbon;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $websites = [
            ['name' => 'Google', 'url' => 'https://google.com'],
            ['name' => 'GitHub', 'url' => 'https://github.com'],
            ['name' => 'My Portfolio', 'url' => 'https://example.com'],
        ];

        foreach ($websites as $site) {
            $website = Website::create([
                'user_id' => 1, // adjust for your user IDs
                'name'    => $site['name'],
                'url'     => $site['url'],
                'status'  => 'unknown',
                'check_interval' => 900,
                'is_active' => true
            ]);

            // Generate multiple historical checks
            for ($i = 0; $i < 50; $i++) {
                $status = rand(0, 10) > 2 ? 'up' : 'down'; // ~80% uptime
                $responseTime = rand(100, 2000); // ms

                UptimeCheck::create([
                    'website_id'    => $website->id,
                    'status'        => $status,
                    'response_time' => $responseTime,
                    'status_code'   => $status === 'up' ? 200 : 500,
                    'error_message' => $status === 'down' ? 'Timeout reached' : null,
                    'checked_at'    => Carbon::now()->subHours(rand(0, 168)), // within past week
                ]);
            }
        }
    }
}