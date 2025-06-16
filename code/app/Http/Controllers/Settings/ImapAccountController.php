<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ImapAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ImapAccountController extends Controller
{
    public function edit(Request $request): Response
    {
        $accounts = $request->user()->imapAccounts()->get(['id', 'host', 'username']);

        return Inertia::render('settings/Imap', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'host' => ['required', 'string'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['nullable', 'in:none,ssl,tls'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($data['encryption'] === 'none') {
            $data['encryption'] = null;
        }

        $request->user()->imapAccounts()->create($data);

        return back(303);
    }

    public function destroy(ImapAccount $account): RedirectResponse
    {
        $account->delete();

        return back(303);
    }
}
