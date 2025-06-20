<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Charge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
}
