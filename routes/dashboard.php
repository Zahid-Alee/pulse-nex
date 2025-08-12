<?php

// use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('websites')->group(function () {
        Route::get('/', [WebsiteController::class, 'view'])->name('website.list'); // List view
        Route::get('/create', [WebsiteController::class, 'createView'])->name('website.create'); // Create view
        Route::get('/{website}/edit', [WebsiteController::class, 'editView'])->name('website.edit'); // Edit view
        Route::get('/{website}', [WebsiteController::class, 'showView'])->name('website.show'); // View single website
        Route::post('/{website}/check-now', [WebsiteController::class, 'checkNow'])->name('website.checkNow'); // Check now
        Route::delete('/{website}', [WebsiteController::class, 'destroy'])->name('website.delete'); // Delete
    });

    Route::middleware(['auth'])->prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'view'])->name('users.index');
        Route::get('create', [UserController::class, 'createView'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'editView'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('{user}', [UserController::class, 'show'])->name('users.view');

        Route::post('/{user}/upgrade', [UserController::class, 'upgradePlan']);
        Route::post('/{user}/downgrade', [UserController::class, 'downgradePlan']);
    });
});
