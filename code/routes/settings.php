<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\ImapAccountController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');

    Route::get('settings/gmail', [GmailController::class, 'edit'])->name('gmail.edit');
    Route::get('settings/gmail/connect', [GmailController::class, 'redirect'])->name('gmail.connect');
    Route::get('settings/gmail/callback', [GmailController::class, 'callback'])->name('gmail.callback');
    Route::delete('settings/gmail/{token}', [GmailController::class, 'destroy'])->name('gmail.destroy');
    
    Route::get('settings/imap', [ImapAccountController::class, 'index'])->name('imap.index');
    Route::post('settings/imap', [ImapAccountController::class, 'store'])->name('imap.store');
    Route::delete('settings/imap/{account}', [ImapAccountController::class, 'destroy'])->name('imap.destroy');
});
