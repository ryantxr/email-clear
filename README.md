# Email Clear

This repository provides a few simple utilities for connecting to an IMAP inbox.

## Refreshing the token

App\TokenRefresh

This class refreshed the access token if needed.

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

The `code/mailfetch.php` script can be used to verify that the application is able to connect to your mailbox using IMAP. It reads an OAuth2 access token from `code/data/token.json` and refreshes it when needed.

1. Copy `code/.env.example` to `code/.env` and set the `USERNAME` variable to
   your Gmail address.
2. Generate an OAuth2 token using `code/onetime.php` and place the resulting
   `token.json` file in `code/data/`.
3. Install dependencies with `composer install --no-dev --ignore-platform-reqs`.
4. Run the script:

```bash
php code/mailfetch.php
```

If the connection is successful, the subject of the fetched message will be displayed.
