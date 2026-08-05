<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\NavigationLookupContract;
use Modules\Cms\Models\NavItem;
use Modules\Cms\Models\NavMenu;

class NavigationLookupService implements NavigationLookupContract
{
    public function itemsForLocation(string $location): array
    {
        $menu = NavMenu::where('location', $location)->where('is_active', true)->first();

        if (! $menu) {
            return [];
        }

        $items = NavItem::where('menu_id', $menu->id)->where('is_active', true)->orderBy('order_index')->get();

        return $items->whereNull('parent_id')->map(fn (NavItem $item) => [
            ...$item->toArray(),
            'children' => $items->where('parent_id', $item->id)->values()->map->toArray()->all(),
        ])->values()->all();
    }
}
