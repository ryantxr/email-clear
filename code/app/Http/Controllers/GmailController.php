<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserToken;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;

class GmailController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['https://mail.google.com/', 'email'])
            ->redirect();
    }

    public function callback2(Request $request)
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
    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $token = [
            'access_token'  => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'expires_in'    => $googleUser->expiresIn,
            'created'       => time(),
        ];

        Auth::user()->tokens()->create([
            'email' => $googleUser->email,
            'refresh_token' => $googleUser->refreshToken,
            'token' => $token,
        ]);

        return redirect()->route('gmail.edit');
    }

    public function edit(): Response
    {
        $tokens = Auth::user()->tokens()->get(['id', 'email']);

        return Inertia::render('settings/Gmail', [
            'tokens' => $tokens,
        ]);
    }

    public function destroy(UserToken $token)
    {
        $token->delete();

        return back();
    }
}
