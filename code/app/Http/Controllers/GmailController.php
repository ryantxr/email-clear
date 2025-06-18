<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UserToken;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Illuminate\Support\Facades\Log;
class GmailController extends Controller
{
    public function redirect()
    {
        //Log::debug(json_encode(config('services.google')));
        /** @var Laravel\Socialite\Contracts\Provider */
        $provider = Socialite::driver('google');
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
    
    public function callbackShadow(Request $request)
    {
        Log::info(__METHOD__);
        $data = $request->validate([
            'code' => 'sometimes|string',
            'access_token' => 'required_without:code|string',
            'refresh_token' => 'required_without:code|string',
            'expires_in' => 'required_without:code|integer',
            'email' => 'sometimes|email',
        ]);
        Log::debug(print_r($data, true));
        /** @var Laravel\Socialite\Contracts\Provider */
        $provider = Socialite::driver('google');
        $provider->stateless();

        if (isset($data['code'])) {
            $tokenData = $provider->getAccessTokenResponse($data['code']);
            $accessToken = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['expires_in'] ?? null;
            $googleUser = $provider->userFromToken($accessToken);
            $email = $googleUser->email;
        } else {
            $accessToken = $data['access_token'];
            $refreshToken = $data['refresh_token'];
            $expiresIn = $data['expires_in'];
            $email = $data['email'] ?? null;
        }

        $token = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn,
            'created' => time(),
        ];

        /** @var Illuminate\Contracts\Auth\Authenticatable */
        $u = Auth::user();
        Log::info(json_encode([
            'user_id' => $u->id,
            'email' => $email,
            'refresh_token' => $refreshToken,
            'token' => $token,
        ]));

        UserToken::create([
            'user_id' => $u->id,
            'email' => $email,
            'refresh_token' => $refreshToken,
            'token' => $token,
        ]);

        // $u->tokens()->create([
        //     'email' => $email,
        //     'refresh_token' => $refreshToken,
        //     'token' => $token,
        // ]);

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
