<?php

namespace Modules\Cms\Contracts;

interface NavigationLookupContract
{
    /**
     * Top-level nav items (with `children` embedded) for the named menu
     * location (e.g. 'header', 'footer').
     */
    public function itemsForLocation(string $location): array;
}
