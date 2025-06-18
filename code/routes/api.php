<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GmailController;

if (env('APP_ENV') === 'local') {
    Route::post('gmail/callback-shadow', [GmailController::class, 'callbackShadow'])
        ->name('gmail.callback-shadow');
}
