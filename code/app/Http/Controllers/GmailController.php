<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserToken;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Two\GoogleProvider;
use Illuminate\Support\Facades\Log;
class GmailController extends Controller
{
    public function redirect()
    {
        $u = Auth::user();
        $id = $u->id;
        //Log::debug(json_encode(config('services.google')));
        /** @var \Laravel\Socialite\Two\GoogleProvider */
        $provider = Socialite::driver('google');
        
        
        $dynamicRedirectUri = config('services.google.redirect') . "?user_id={$id}";
        Log::debug("redirectUri {$dynamicRedirectUri}");
        $provider->redirectUrl($dynamicRedirectUri);
        

        return $provider
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
        
        /** @var Illuminate\Contracts\Auth\Authenticatable */
        $u = Auth::user();
        $u->tokens()->create([
            'email' => $googleUser->email,
            'refresh_token' => $googleUser->refreshToken,
            'token' => $token,
        ]);
        
        return redirect()->route('gmail.edit', status: 303);
    }
    
    
    public function edit(): Response
    {
        /** @var Illuminate\Contracts\Auth\Authenticatable */
        $u = Auth::user();
        $tokens = $u->tokens()->get(['id', 'email']);

        return Inertia::render('settings/Gmail', [
            'tokens' => $tokens,
        ]);
    }

    public function destroy(UserToken $token)
    {
        $token->delete();

        return back(303);
    }
}
