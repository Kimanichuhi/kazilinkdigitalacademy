<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Sets a CSP nonce (picked up automatically by @vite/@livewireScripts)
     * before the response is built, then attaches the security headers
     * once the response comes back down the middleware stack.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If a Vite hot file exists in local dev, keep it in sync with the
        // explicitly configured VITE_DEV_SERVER_ORIGIN (e.g. an ngrok
        // tunnel host set by the developer) so laravel-vite-plugin
        // generates asset URLs that match. Never derived from the request.
        if (app()->isLocal()) {
            $hotPath = public_path('hot');
            if (file_exists($hotPath)) {
                try {
                    $current = trim(file_get_contents($hotPath));

                    // Only auto-update the hot file when the developer has
                    // explicitly set the Vite origin via env. Avoid
                    // overwriting the hot file with the app host which
                    // breaks @vite in dev mode.
                    $envOrigin = env('VITE_DEV_SERVER_ORIGIN');
                    if ($envOrigin && $current !== $envOrigin) {
                        @file_put_contents($hotPath, $envOrigin);
                    }
                } catch (\Throwable $e) {
                    // best-effort only; do not interrupt the request
                }
            }
        }

        $nonce = Vite::useCspNonce();

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce));

        // Only ever advertise HSTS over an actually-secure connection —
        // sending it over plain HTTP in local dev would get cached by the
        // browser and lock the dev server out until the header expires.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        // Alpine (bundled by Livewire) evaluates x-data/x-show expressions
        // via `new Function(...)`, which CSP classifies as eval — there's
        // no way to run this app's Alpine directives without it short of
        // switching to Alpine's separate CSP-build with precompiled
        // expressions, which is a much larger change than this pass.
        $scriptSrc = "'self' 'unsafe-eval' 'nonce-{$nonce}'";
        $styleSrc = "'self' 'unsafe-inline'";
        $connectSrc = "'self'";

        if (app()->isLocal()) {
            // Vite's dev server (HMR) runs on its own origin/port locally.
            // Trusted origins here come only from server-side configuration
            // (VITE_DEV_SERVER_ORIGIN / the hot file our own build tooling
            // writes) — never from the incoming request's Host header,
            // which a client fully controls.
            $scriptSrc .= ' http://localhost:5173 http://127.0.0.1:5173';
            $styleSrc .= ' http://localhost:5173 http://127.0.0.1:5173';
            $connectSrc .= ' http://localhost:5173 http://127.0.0.1:5173 ws://localhost:5173 ws://127.0.0.1:5173';
            // Also include the currently configured Vite origin, which
            // may live in the `public/hot` file (dev mode) or be set via
            // `VITE_DEV_SERVER_ORIGIN` in .env when using tunnels.
            $viteOrigin = null;
            $hotPath = public_path('hot');
            if (file_exists($hotPath)) {
                try {
                    $hot = trim(@file_get_contents($hotPath));
                    if (! empty($hot)) {
                        // Replace 0.0.0.0 (bind address) with localhost so
                        // browser clients can reach the dev server.
                        if (strpos($hot, '0.0.0.0') !== false) {
                            $hot = str_replace('0.0.0.0', 'localhost', $hot);
                            @file_put_contents($hotPath, $hot);
                        }

                        $viteOrigin = $hot;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            if (empty($viteOrigin)) {
                $viteOrigin = env('VITE_DEV_SERVER_ORIGIN');
            }

            if (! empty($viteOrigin)) {
                // ensure it has a scheme
                if (! preg_match('#^https?://#', $viteOrigin)) {
                    $viteOrigin = 'http://' . ltrim($viteOrigin, '/');
                }

                $scriptSrc .= ' ' . $viteOrigin;
                $styleSrc .= ' ' . $viteOrigin;
                $connectSrc .= ' ' . $viteOrigin;

                // also allow websocket variant (ws/wss)
                $ws = preg_replace('#^https?#', 'ws', $viteOrigin);
                $connectSrc .= ' ' . $ws;
            }
        }

        return implode('; ', [
            "default-src 'self'",
            "script-src {$scriptSrc}",
            // Alpine toggles the `style` attribute directly (x-show etc.),
            // and several views set inline background-image/color styles
            // (hero/ad/CTA banners), so style-src needs 'unsafe-inline'.
            // Deliberately no nonce here: per the CSP spec, browsers that
            // support nonce-sources ignore 'unsafe-inline' whenever a
            // nonce is also present in the same directive — pairing them
            // silently disabled every inline style attribute in the app.
            // Nothing in this codebase renders a nonced <style> tag, so
            // there's no nonce-only content to protect here anyway.
            "style-src {$styleSrc}",
            "img-src 'self' data: https:",
            "font-src 'self' data:",
            "connect-src {$connectSrc}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}
