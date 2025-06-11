<?php
namespace App;
/*
 * This file is a proof of concept only.
 * Use as a reference only.
 * Do not modify
 */
use GuzzleHttp\Client as HttpClient;
use RuntimeException;

class TokenRefresher
{
    protected string $clientSecretPath;
    protected string $tokenPath;
    protected HttpClient $httpClient;
    protected $token;

    public function __construct(string $clientSecretPath, string $tokenPath, ?HttpClient $httpClient = null)
    {
        $this->clientSecretPath = $clientSecretPath;
        $this->tokenPath = $tokenPath;
        $this->httpClient = $httpClient ?: new HttpClient();
    }

    public function needsRefresh(): bool
    {
        if (!file_exists($this->tokenPath)) {
            throw new RuntimeException('Token file not found.');
        }
    
        $this->token = json_decode(file_get_contents($this->tokenPath), true);
        if (!is_array($this->token)) {
            throw new RuntimeException('Invalid token file.');
        }
        $expiry = ($this->token['created'] ?? 0) + ($this->token['expires_in'] ?? 0) - 60;
        // Token is not expired
        if (time() < $expiry || !isset($this->token['refresh_token'])) {
            return false;
        }
    
        if (!file_exists($this->clientSecretPath)) {
            throw new RuntimeException('Client secret file not found.');
        }

        return true;
    }

    public function accessToken(): array
    {
        return $this->token;
    }

    /**
     * Refresh the access token if it has expired.
     *
     * @return array The token data after refresh.
     * @throws RuntimeException If required files are missing or data is invalid.
     */
    public function refresh(): array
    {
        if (!file_exists($this->tokenPath)) {
            throw new RuntimeException('Token file not found.');
        }

        $this->token = json_decode(file_get_contents($this->tokenPath), true);
        if (!is_array($this->token)) {
            throw new RuntimeException('Invalid token file.');
        }

        $expiry = ($this->token['created'] ?? 0) + ($this->token['expires_in'] ?? 0) - 60;
        if (time() < $expiry || !isset($this->token['refresh_token'])) {
            return $this->token;
        }

        if (!file_exists($this->clientSecretPath)) {
            throw new RuntimeException('Client secret file not found.');
        }

        $secret = json_decode(file_get_contents($this->clientSecretPath), true);
        if (!is_array($secret)) {
            throw new RuntimeException('Invalid client secret file.');
        }
        
        $cfg = $secret['installed'] ?? $secret['web'] ?? [];
        $tokenUri = $cfg['token_uri'] ?? 'https://oauth2.googleapis.com/token';
        
        if ( empty($cfg['client_id']) || empty($cfg['client_secret']) ) {
            throw new RuntimeException('client_id or client_secret is missing or empty.');
        }
        $response = $this->httpClient->post($tokenUri, [
            'form_params' => [
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'refresh_token' => $this->token['refresh_token'],
                'grant_type'    => 'refresh_token',
            ],
        ]);

        $new = json_decode($response->getBody(), true);
        if (!isset($new['access_token'])) {
            throw new RuntimeException('Failed to refresh token.');
        }

        $this->token['access_token'] = $new['access_token'];
        $this->token['expires_in']   = $new['expires_in'] ?? 3600;
        $this->token['created']      = time();
        file_put_contents($this->tokenPath, json_encode($this->token));

        return $this->token;
    }
}
