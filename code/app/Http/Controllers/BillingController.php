<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Billing', [
            'plans' => Plan::orderBy('price')->get(['id', 'name', 'description', 'price', 'features']),
            'currentPlan' => $user ? $user->plan() : 'free',
        ]);
    }
}
