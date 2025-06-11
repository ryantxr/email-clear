<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use Illuminate\Http\Request;
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

    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $token = [
            'access_token'  => $googleUser->token,
            'refresh_token' => $googleUser->refreshToken,
            'expires_in'    => $googleUser->expiresIn,
            'created'       => time(),
        ];

        UserToken::create([
            'user_id' => Auth::id(),
            'email'   => $googleUser->email,
            'token'   => $token,
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
