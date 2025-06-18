<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GmailController;

Route::post('settings/gmail/callback-shadow', [GmailController::class, 'callbackShadow'])->name('gmail.callback-shadow');
