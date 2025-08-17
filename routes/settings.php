<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance');


    Route::get('settings/user-plan', function () {
        $user = auth()->user();
        $subscription = $user->subscription; 

        $plans = [
            [
                'name' => 'Free',
                'slug' => 'Free',
                'price' => 0,
                'interval' => '/month',
                'info' => 'Up to 5 websites · Every 5 minutes',
                'monitors_limit' => 5,
                'check_interval' => 5,
                'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 7 days)'],
                'popular' => false,
            ],
            [
                'name' => 'Pro',
                'slug' => 'Pro',
                'price' => 5,
                'interval' => '/month',
                'info' => 'Up to 15 websites · Every 3 minutes',
                'monitors_limit' => 15,
                'check_interval' => 3,
                'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 30 days)', 'Priority Support'],
                'popular' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'Business',
                'price' => 15,
                'interval' => '/month',
                'info' => 'Up to 50 websites · Every 1 minute',
                'monitors_limit' => 50,
                'check_interval' => 1,
                'features' => ['Uptime/Downtime Monitoring', 'Email Alerts', 'Uptime Reports (last 30 days)', 'Priority Support', 'Advanced Analytics'],
                'popular' => false,
            ],
        ];

        return Inertia::render('settings/user-plan', [
            'userPlan' => $subscription ? $subscription->plan_name : 'Free',
            'plans' => $plans,
        ]);
    })->name('user-plan');
});
