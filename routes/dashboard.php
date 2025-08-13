<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    // Contact routes


    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/{id}/status', [ContactController::class, 'updateStatus'])->name('admin.contacts.updateStatus');

    Route::prefix('websites')->group(function () {
        Route::get('/', [WebsiteController::class, 'view'])->name('website.list');
        Route::get('/create', [WebsiteController::class, 'createView'])->name('website.create');
        Route::get('/{website}/edit', [WebsiteController::class, 'editView'])->name('website.edit');
        Route::get('/{website}', [WebsiteController::class, 'showView'])->name('website.show');
        Route::post('/{website}/check-now', [WebsiteController::class, 'checkNow'])->name('website.checkNow');
        Route::delete('/{website}', [WebsiteController::class, 'destroy'])->name('website.delete');
    });

    // Admin Contacts routes
    Route::prefix('admin/contacts')->name('admin.contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'view'])->name('index');
        Route::post('/{id}/status', [ContactController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/{id}', [ContactController::class, 'show'])->name('show');
    });

    // Admin Users routes
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'view'])->name('index');
        Route::get('create', [UserController::class, 'createView'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'editView'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('{user}', [UserController::class, 'show'])->name('view');

        Route::post('/{user}/upgrade', [UserController::class, 'upgradePlan'])->name('upgradePlan');
        Route::post('/{user}/downgrade', [UserController::class, 'downgradePlan'])->name('downgradePlan');
    });
});
