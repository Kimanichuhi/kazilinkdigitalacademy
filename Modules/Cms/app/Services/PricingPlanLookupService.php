<?php

namespace Modules\Cms\Services;

use Modules\Cms\Contracts\PricingPlanLookupContract;
use Modules\Cms\Models\PricingPlan;

class PricingPlanLookupService implements PricingPlanLookupContract
{
    public function listPublished(): array
    {
        return PricingPlan::where('is_published', true)
            ->orderBy('order_index')
            ->get()
            ->toArray();
    }
}
