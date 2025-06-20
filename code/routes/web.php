<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\SiteContentController;

Route::get('/', HomeController::class)->name('home');

Route::get('billing', BillingController::class)->name('billing');
Route::middleware('auth')->group(function () {
    Route::post('billing/upgrade', [SubscriptionController::class, 'intent'])->name('billing.upgrade');
    Route::get('billing/success', [SubscriptionController::class, 'success'])->name('billing.success');
    Route::post('billing/cancel', [SubscriptionController::class, 'cancel'])->name('billing.cancel');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('admin/content', [SiteContentController::class, 'edit'])->name('admin.content.edit');
    Route::post('admin/content', [SiteContentController::class, 'update'])->name('admin.content.update');
});
