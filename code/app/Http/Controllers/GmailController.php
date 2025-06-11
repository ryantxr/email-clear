<?php

namespace App\Http\Controllers;

use App\Models\GmailToken;
use Illuminate\Http\Request;

class GmailController extends Controller
{
    public function callback(Request $request)
    {
        $user = $request->user();

        if ($user->gmailTokens()->count() >= $user->planLimit('max_tokens', PHP_INT_MAX)) {
            return redirect()->route('dashboard')->withErrors('Maximum number of Gmail connections reached.');
        }

        // Handle OAuth callback and persist token
        // $tokenData = ...
        // GmailToken::create(['user_id' => $user->id, 'token' => $tokenData]);

        return redirect()->route('dashboard');
    }
}
