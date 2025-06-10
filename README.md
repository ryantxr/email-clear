# Email Clear

This repository provides a few simple utilities for connecting to an IMAP inbox.

## Fetching a single email

The `code/mailfetch.php` script can be used to verify that the application is able to connect to your mailbox. It will retrieve the latest email from the `INBOX` folder and print its subject.

1. Copy `code/.env.example` to `code/.env` and provide your IMAP credentials.
2. Install dependencies with `composer install --no-dev --ignore-platform-reqs`.
3. Run the script:

```bash
php code/mailfetch.php
```

If the connection is successful, the subject of the fetched message will be displayed.
