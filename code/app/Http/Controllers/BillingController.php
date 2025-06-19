<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Billing', [
            'plans' => Plan::orderBy('price')->get(['id', 'name', 'description', 'price', 'features']),
        ]);
    }
}
