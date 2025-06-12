<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Setting;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\Admin\SiteContentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'frontPageText' => Setting::where('key', 'front_page_text')->value('value'),
        'pricing' => Setting::where('key', 'pricing')->value('value'),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('admin/content', [SiteContentController::class, 'edit'])->name('admin.content.edit');
    Route::post('admin/content', [SiteContentController::class, 'update'])->name('admin.content.update');
});
