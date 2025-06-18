<?php

namespace App\Http\Controllers\Settings;
/*
Yes, I know this controller isn't used.
I plan to keep this as a reference since it implements
oauth with directly with the google client.
*/
use App\Http\Controllers\Controller;
use App\Models\UserToken;
use Google_Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GmailController extends Controller
{
    /**
     * Show Gmail connection settings.
     */
    public function edit(Request $request): Response
    {
        $token = $request->user()->token;

        return Inertia::render('settings/Gmail', [
            'connected' => !is_null($token),
        ]);
    }

    /**
     * Redirect the user to Google for OAuth consent.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $client = $this->makeClient();
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Handle the OAuth callback and store the refresh token.
     */
    public function callback(Request $request): RedirectResponse
    {
        $client = $this->makeClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        $request->user()->token()->updateOrCreate([], [
            'refresh_token' => $token['refresh_token'] ?? ($token['access_token'] ?? ''),
        ]);

        return to_route('gmail.edit', [], 303);
    }

    /**
     * Disconnect the user's Gmail account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $token = $request->user()->token;
        if ($token) {
            $token->delete();
        }
        return back(303);
    }

    protected function makeClient(): Google_Client
    {
        $client = new Google_Client();
        $client->setAuthConfig(config('services.google.credentials'));
        $client->addScope('https://mail.google.com/');
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri(config('services.google.redirect'));
        return $client;
    }
}
