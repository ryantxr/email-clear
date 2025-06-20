<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Charge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\StripeClient;

class SubscriptionController extends Controller
{
    public function upgrade(Request $request): RedirectResponse
    {
        $user = $request->user();
        $plan = Plan::where('name', 'Pro')->firstOrFail();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'start_at' => now(),
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

    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();
        $subscription = $user->subscription;
        if ($subscription && $subscription->status === 0) {
            $subscription->update([
                'end_at' => now(),
                'status' => 2,
            ]);

            Charge::create([
                'user_id' => $user->id,
                'plan_id' => $subscription->plan_id,
                'amount' => 0,
                'status' => 501,
            ]);
        }

        $user->plan = 'free';
        $user->save();

        return to_route('billing', [], 303);
    }

    public function checkout(Request $request): Response
    {
        $plan = Plan::where('name', 'Pro')->firstOrFail();

        return Inertia::render('BillingCheckout', [
            'plan' => $plan,
            'publishableKey' => config('services.stripe.key'),
        ]);
    }

    public function intent(Request $request)
    {
        $plan = Plan::where('name', 'Pro')->firstOrFail();
        $stripe = new StripeClient(config('services.stripe.secret'));
        $intent = $stripe->paymentIntents->create([
            'amount' => (int) ($plan->price * 100),
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return response()->json(['clientSecret' => $intent->client_secret]);
    }

    public function success(Request $request): RedirectResponse
    {
        $user = $request->user();
        $plan = Plan::where('name', 'Pro')->firstOrFail();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'price' => $plan->price,
            'start_at' => now(),
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
