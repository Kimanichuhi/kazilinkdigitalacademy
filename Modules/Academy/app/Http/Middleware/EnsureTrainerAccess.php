<?php

namespace Modules\Academy\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\Core\Support\RoleGroups;
use Symfony\Component\HttpFoundation\Response;

/**
 * /trainer never had a real guard in the source (the area was never built —
 * see MIGRATION-INVENTORY.md §3.6). Guard it the same way /admin is guarded:
 * trainer or admin-family (for oversight) may in, everyone else -> /student.
 */
class EnsureTrainerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->hasRole(RoleGroups::TRAINER) && ! $user?->hasAnyRole(RoleGroups::adminFamily())) {
            return Redirect::to('/student');
        }

        return $next($request);
    }
}
