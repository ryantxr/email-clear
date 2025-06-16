<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class SiteContentController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('admin/SiteContent', [
            'frontPageText' => Setting::where('key', 'front_page_text')->value('value'),
            'pricing' => Setting::where('key', 'pricing')->value('value'),
        ]);
    }

    public function update(Request $request)
    {
        Log::info(__METHOD__);
        $data = $request->validate([
            'front_page_text' => ['nullable', 'string'],
            'pricing' => ['nullable', 'string'],
        ]);
        Log::debug("validated");
        foreach ($data as $key => $value) {
            Log::debug("Update $key");
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back(303);
    }
}
