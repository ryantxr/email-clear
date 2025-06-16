# Email Clear

This repository provides a few simple utilities for connecting to an IMAP inbox.

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

The application under `code/` now provides a simple Gmail connection flow. After registering and verifying your account, visit `/settings/gmail` to connect your mailbox. A console command `php artisan gmail:scan` will read recent messages and update the `user_tokens` table with the last scanned time.

## Standard IMAP Accounts

In addition to Gmail OAuth, you can connect any standard IMAP inbox. Visit `/settings/imap` to add your server host, port, encryption type, username and password. Credentials are encrypted in the database.

Run `php artisan imap:scan` to process all saved accounts using the same OpenAI powered classifier.
