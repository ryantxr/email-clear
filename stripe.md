# Stripe Link Integration

This project can use [Link by Stripe](https://docs.stripe.com/payments/link) to let customers pay faster with saved payment methods.

## Enable Link in Stripe

1. Sign in to your Stripe dashboard and switch to **Test mode** while developing.
2. From **Settings > Payment methods**, enable **Link**.
3. Add your application's domain under **Link settings** so Stripe can load the Link modal on your site.
4. Note your **Publishable key** and **Secret key** from **Developers > API keys**.
5. If you use webhooks, create one for `payment_intent.succeeded` and copy the webhook signing secret.
6. Create a recurring price for the **Pro** plan and note the Price ID.

## Configure the application

1. Install the Stripe libraries:

   ```bash
   composer require stripe/stripe-php
   npm install @stripe/stripe-js
   ```

2. Copy the new environment variables from `.env.example` to your `.env` and set your Stripe credentials:

      STRIPE_KEY=pk_test_...
      STRIPE_SECRET=sk_test_...
      STRIPE_WEBHOOK_SECRET=whsec_...
      STRIPE_PRO_PRICE=price_...

3. The keys are accessed via `config/services.php` under the `stripe` section.
4. The **Upgrade** button now redirects users to `/billing/checkout` where the Payment Element is mounted. Link automatically offers saved payment methods there.
5. After Stripe confirms the payment, the user is redirected back to `/billing/success` which activates their subscription. Handle webhooks for additional verification.

## References

- https://docs.stripe.com/payments/link/link-payment-integrations
- https://docs.stripe.com/payments/link/checkout-link
- https://docs.stripe.com/payments/link/elements-link
- https://docs.stripe.com/payments/link/link-authentication-element
- https://docs.stripe.com/payments/link/express-checkout-element-link
- https://docs.stripe.com/payments/link/payment-element-link
