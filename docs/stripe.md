# Stripe Integration

This project uses Stripe Checkout to upgrade users to the **Pro** plan.

1. Configure your Stripe credentials in `.env`:

```
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_PRO_PRICE=price_123
```

2. The upgrade button sends a POST request to `/billing/upgrade`. The controller action creates a Checkout Session in subscription mode using the price ID from `config('services.stripe.price')`. After payment, the app stores the `customer` and `subscription` IDs returned by Stripe.

3. Stripe handles all recurring charges. The `success` route finalises the local subscription record and updates the user's plan.

4. Manual `Charge` records are no longer created when a subscription succeeds.
