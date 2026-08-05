<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Auth\Services\PostLoginRedirector;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * The source always redirects by role rather than to an "intended" URL
     * (app/auth/login/page.tsx:44-49) — matched here rather than using
     * redirect()->intended().
     */
    public function store(LoginRequest $request, PostLoginRedirector $redirector): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect($redirector->path(Auth::user()));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
