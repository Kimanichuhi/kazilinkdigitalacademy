<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Modules\Academy\Http\Middleware\EnsureTrainerAccess;
use Modules\Admin\Http\Middleware\EnsureAdminFamily;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.family' => EnsureAdminFamily::class,
            'trainer.access' => EnsureTrainerAccess::class,
        ]);

        $middleware->append(SecurityHeaders::class);

        // No fixed proxy IP list to trust here — the app always sits behind
        // some TLS-terminating reverse proxy (ngrok tunnels, Railway,
        // Render, cPanel's LiteSpeed front end), never talks to browsers
        // directly. Without this, $request->secure() reports false behind
        // any of them, which silently drops HSTS and the Secure cookie flag.
        $middleware->trustProxies(at: '*');

        // Safaricom's Daraja callback is an unauthenticated server-to-server
        // POST with no CSRF token, but it's registered on Payment's web.php
        // (to match the MPESA_CALLBACK_URL path already set in .env), so it
        // needs an explicit exemption from the 'web' group's CSRF check.
        $middleware->validateCsrfTokens(except: [
            'payments/mpesa/callback/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
