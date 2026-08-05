<?php

namespace Modules\Core\Support;

/**
 * Resolves a cross-module Contract only if its owning module is currently
 * enabled (i.e. actually bound a concrete in the container). Content/CMS
 * modules like Marketing and Cms are enrichment layers over the core site
 * (Core/Academy/Booking) — disabling one of them must not 500 the homepage,
 * navbar, or footer that every page shares. Callers that hard-depend on a
 * module for their core function (e.g. Booking needing Academy) should keep
 * using normal constructor/method injection instead of this helper — that
 * failure is a legitimate one to surface loudly.
 */
class OptionalContract
{
    public static function resolve(string $contract): ?object
    {
        return app()->bound($contract) ? app($contract) : null;
    }
}
