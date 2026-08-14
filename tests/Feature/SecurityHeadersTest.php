<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    /**
     * Regression test for a CSP hardening fix: the local-dev CSP branch used
     * to append `request()->getSchemeAndHttpHost()` (the client-supplied
     * Host header) to script-src/style-src/connect-src. Trusted origins must
     * only ever come from server-side configuration (VITE_DEV_SERVER_ORIGIN
     * / the Vite hot file), never from the request itself.
     */
    public function test_local_csp_never_reflects_the_request_host_header(): void
    {
        $this->app['env'] = 'local';

        $response = $this->withHeaders(['Host' => 'evil.example.com'])->get('/login');

        $response->assertOk();

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringNotContainsString('evil.example.com', $csp);
    }

    public function test_non_local_csp_has_no_dev_server_origins(): void
    {
        // phpunit.xml sets APP_ENV=testing, so isLocal() is false here.
        $response = $this->get('/login');

        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);
        $this->assertStringNotContainsString('localhost:5173', $csp);
        $this->assertStringNotContainsString('127.0.0.1:5173', $csp);
    }
}
