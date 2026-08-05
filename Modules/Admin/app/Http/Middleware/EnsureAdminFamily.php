<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\Core\Support\RoleGroups;
use Symfony\Component\HttpFoundation\Response;

/**
 * Matches the source admin layout guard exactly (app/admin/layout.tsx):
 * unauthenticated -> /auth/login (handled by the `auth` middleware already
 * in front of this one); authenticated but not admin-family -> /student.
 */
class EnsureAdminFamily
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAnyRole(RoleGroups::adminFamily())) {
            return Redirect::to('/student');
        }

        return $next($request);
    }
}
