<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

class GmailController extends Controller
{
    public function callbackShadow(Request $request)
    {
        if (env('APP_ENV') !== 'local') {
            abort(403);
        }
        Log::info(__METHOD__);
        $data = $request->validate([
            'user_id' => 'sometimes|integer',
            'state' => 'sometimes|string',
            'code' => 'sometimes|string',
            'access_token' => 'required_without:code|string',
            'refresh_token' => 'required_without:code|string',
            'expires_in' => 'required_without:code|integer',
            'email' => 'sometimes|email',
        ]);
        Log::debug(print_r($data, true));
        /** @var \Laravel\Socialite\Two\GoogleProvider */
        $provider = Socialite::driver('google');
        $provider->stateless();
        $provider->redirectUrl(config('services.google.redirect'));

        $userId = isset($data['state']) ? (int)$data['state'] : ($data['user_id'] ?? null);
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

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        Log::info(json_encode([
            'user_id' => $userId,
            'email' => $email,
            'refresh_token' => $refreshToken,
            'token' => $token,
        ]));

        UserToken::create([
            'user_id' => $userId,
            'email' => $email,
            'refresh_token' => $refreshToken,
            'token' => $token,
        ]);

        return redirect()->route('gmail.edit', status: 303);
    }
}
