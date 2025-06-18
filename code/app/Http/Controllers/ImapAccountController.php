<?php

namespace App\Http\Controllers;

use App\Models\ImapAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ImapAccountController extends Controller
{
    public function index(): Response
    {
        $accounts = Auth::user()->imapAccounts()->get(['id', 'email']);

        return Inertia::render('settings/Imap', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (method_exists($request->user(), 'canAddEmail') && !$request->user()->canAddEmail()) {
            return back(303);
        }
        $data = $request->validate([
            'email' => ['required', 'email'],
            'host' => ['required', 'string'],
            'port' => ['required', 'integer'],
            'encryption' => ['nullable', 'string'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $request->user()->imapAccounts()->create($data);

        return back(303);
    }

    public function destroy(ImapAccount $account): RedirectResponse
    {
        $account->delete();

        return back(303);
    }
}
