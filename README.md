# Email Clear

This repository provides a few simple utilities for connecting to an IMAP inbox.
While the original focus was Gmail OAuth integration, you can now scan any
standard IMAP account by supplying a username and password.

## Token refresher

`App\TokenRefresher` checks whether your OAuth access token has expired and
refreshes it when necessary.


usage:
```php
   $refresher = new TokenRefresher($clientSecret, $tokenPath);
   if ( $refresher->needsRefresh() ) {
      echo 'Token expired. Refreshing.' . PHP_EOL;
      $token = $refresher->refresh();
   } else {
      $token = $refresher->accessToken();
      echo 'Token is good.' . PHP_EOL;
   }
   echo $token['access_token'] . PHP_EOL;
```

## Fetching a single email

The `scripts/webklex_fetch.php` script demonstrates how to retrieve the most
recent message using the `webklex/php-imap` library. It relies on
`App\TokenRefresher` to refresh the OAuth token only when necessary so that
Google login attempts are kept to a minimum.

1. Copy `code/.env.example` to `code/.env` and set the `USERNAME` variable to
   your Gmail address.
2. Generate an OAuth2 token using `scripts/onetime.php` and place the resulting
   `token.json` file in `scripts/data/`.
3. Install dependencies with `composer install --no-dev --ignore-platform-reqs`.
4. Run the script:

```bash
php scripts/webklex_fetch.php
```

If the connection is successful, the subject of the fetched message will be
displayed.

## Laravel integration

The application under `code/` now provides a simple Gmail connection flow. After registering and verifying your account, visit `/settings/gmail` to connect your mailbox. A console command `php artisan gmail:scan` will read recent messages and update the `user_tokens` table with the last scanned time. **Scanning now uses the Gmail API instead of IMAP.**

A localhost OAuth listener may also capture the tokens or authorization code and POST them to `/api/settings/gmail/callback-shadow`. Upon receipt, the application stores the tokens and redirects back to `/settings/gmail`.

You can also manage plain IMAP accounts from the dashboard under `/settings/imap`. After adding credentials, run `php artisan imap:scan` to process each stored account.

### IMAP-only usage

Add account details under `/settings/imap` and then run:

```bash
php artisan imap:scan
```

Each stored account will be scanned in turn.
