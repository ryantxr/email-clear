<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Stripe\StripeClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    public function intent(Request $request): RedirectResponse
    {
        $user = $request->user();
        $plan = Plan::where('name', 'Pro')->firstOrFail();

        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => config('services.stripe.price'),
                    'quantity' => 1,
                ],
            ],
            'customer_email' => $user->email,
            'success_url' => url('/billing/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/billing'),
        ]);

        return redirect($session->url);
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();
        $subscription = $user->subscription;
        if ($subscription && $subscription->status === 0) {
            $subscription->update([
                'end_at' => now(),
                'status' => 2,
            ]);
        }

        $user->plan = 'free';
        $user->save();

        return to_route('billing', [], 303);
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return to_route('billing', [], 303);
        }

        $stripe = new StripeClient(config('services.stripe.secret'));
        $session = $stripe->checkout->sessions->retrieve($sessionId, []);


        $user = $request->user();
        $plan = Plan::where('name', 'Pro')->firstOrFail();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'start_at' => now(),

            'stripe_customer_id' => $session->customer,
            'stripe_subscription_id' => $session->subscription,
            'status' => 0,
        ]);

        Charge::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 100,
        ]);

        $user->plan = 'pro';
        $user->save();

        return to_route('billing', [], 303);
    }
}
