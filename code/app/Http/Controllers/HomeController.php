<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Welcome', [
            'frontPageText' => Setting::where('key', 'front_page_text')->value('value'),
            'pricing' => Setting::where('key', 'pricing')->value('value'),
            'plans' => Plan::orderBy('price')->get(['id', 'name', 'description', 'price', 'features']),
        ]);
    }
}
