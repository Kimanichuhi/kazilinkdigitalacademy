<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Cms\Contracts\PageLookupContract;
use Modules\Cms\Contracts\PricingPlanLookupContract;

class PricingPageController extends Controller
{
    public function show(PricingPlanLookupContract $plans, PageLookupContract $pages): View
    {
        $page = $pages->findBySlug('pricing');
        $blocks = collect($page['blocks'] ?? []);

        return view('cms::pages.pricing', [
            'plans' => $plans->listPublished(),
            'blocks' => $blocks,
        ]);
    }
}
