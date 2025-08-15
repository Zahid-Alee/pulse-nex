<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// Route::get('/test-mail', function () {
//     $user = User::first();
//     try {
//         Mail::to('pruxappomaddi-4731@yopmail.com')->send(new WelcomeEmail($user));
//         return 'Email sent successfully!';
//     } catch (\Exception $e) {
//         return 'Error: ' . $e->getMessage();
//     }
// });


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/features', function () {
    return view('features');
})->name('features');


Route::get('/contact', function () {
    return view('contact');
})->name('contact');


Route::get('/pricing', function () {
    $currentPlan = null;

    if (Auth::check()) {
        $subscription = Auth::user()->subscription;
        if ($subscription) {
            $currentPlan = $subscription->plan_name;
        }
    }

    return view('pricings', compact('currentPlan'));
})->name('pricing');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [WebsiteController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth'])->prefix('/api/websites')->group(function () {
    Route::get('/', [WebsiteController::class, 'index']);
    Route::post('/', [WebsiteController::class, 'store']);
    Route::get('/{website}', [WebsiteController::class, 'show']);
    Route::put('/{website}', [WebsiteController::class, 'update']);
    Route::delete('/{website}', [WebsiteController::class, 'destroy']);


    Route::post('/{website}/check', [WebsiteController::class, 'checkNow']);
    Route::get('/{website}/history', [WebsiteController::class, 'history']);
    Route::get('/{website}/stats', [WebsiteController::class, 'stats']);
});


Route::post('/api/contact', [ContactController::class, 'store']);
Route::get('/api/contacts', [ContactController::class, 'index']);
Route::put('/api/contact/{id}/status', [ContactController::class, 'updateStatus']);

Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

Route::middleware(['auth'])->group(function () {
    Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/dashboard.php';
require __DIR__ . '/auth.php';
