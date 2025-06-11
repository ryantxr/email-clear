# Product Plan

1. Account Creation & Verification

    Use the existing authentication controllers in app/Http/Controllers/Auth.

    Continue enforcing email verification via VerifyEmailController.

    Protect the dashboard route (/dashboard) with the verified middleware.

2. Marketing Landing Page

    Customize resources/js/pages/Welcome.vue to serve as a marketing landing page.

    Display product features, benefits, and clear “Register” / “Login” buttons.

3. Gmail OAuth Authentication Flow

    Implement OAuth using Laravel Socialite or Google’s OAuth client.

    Create a model (e.g., UserToken) to store Gmail refresh tokens for each user (encrypted).

    Provide routes and a settings page for users to connect or disconnect their Gmail account.

    See scripts/onetime.php for reference on obtaining tokens.

4. Scanning & Labeling Mechanism

    Port the MailScanner class from scripts/src/MailScanner.php into app/Services.

        Use Webklex IMAP or Gmail API with XOAUTH2.

        Reuse the OpenAI-based classification logic for detecting solicitation emails.

    After classifying a message, apply or create a “Solicitation” label using the Gmail API.

5. Cron-Based Execution

    Create an Artisan console command (e.g., php artisan gmail:scan) that invokes the new MailScanner service.

    The command should limit each run to a small batch of messages and persist the last scan time (similar to docs/last_scan.json usage in the scripts folder).

    Install a system cron job (outside Laravel’s scheduler) to run this command every five minutes:

        */5 * * * * php /path/to/artisan gmail:scan

6. User Dashboard & Controls

Build a dashboard page where users can:

* See when their Gmail was last scanned.
* Manually trigger a scan (with rate limiting).
* Review how many emails have been labeled.
* Enable or disable automatic scanning.

7. Logging & Error Handling

    Use Laravel’s logging facilities (config/logging.php) to log scan results and errors.

    Expose recent log entries to the user via the dashboard or a dedicated log view.

8. Account & Token Security

    Allow users to revoke Gmail access and remove stored tokens.

    Ensure tokens are encrypted in the database (use Laravel’s encrypted cast or custom encryption).

    Handle token refresh by porting logic from scripts/src/TokenRefresher.php.

9. Additional Considerations

    Respect Gmail API quotas by limiting the number of messages scanned per run and pausing between API calls if necessary.

    Provide in-app documentation (FAQ) explaining Gmail permissions and how data is used.

    Send onboarding emails after registration to guide users through connecting Gmail.
- Initial Laravel integration with Gmail OAuth and scanning command implemented.
