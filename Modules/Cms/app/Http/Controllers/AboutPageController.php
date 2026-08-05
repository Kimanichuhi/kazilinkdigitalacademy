<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Academy\Contracts\TrainerLookupContract;
use Modules\Cms\Contracts\PageLookupContract;
use Modules\Cms\Contracts\TestimonialLookupContract;
use Modules\Cms\Models\TeamMember;
use Modules\Core\Support\OptionalContract;
use Modules\Marketing\Contracts\StatisticLookupContract;

class AboutPageController extends Controller
{
    public function show(TrainerLookupContract $trainers, PageLookupContract $pages): View
    {
        $testimonials = OptionalContract::resolve(TestimonialLookupContract::class);
        $statistics = OptionalContract::resolve(StatisticLookupContract::class);

        $page = $pages->findBySlug('about');
        $blocks = collect($page['blocks'] ?? []);

        return view('cms::pages.about', [
            'blocks' => $blocks,
            'teamMembers' => TeamMember::where('is_active', true)->orderBy('order_index')->get(),
            'trainers' => $trainers->listFeatured(4),
            'testimonials' => $testimonials?->listFeatured(3) ?? [],
            'stats' => collect($statistics?->listActive() ?? [])->keyBy('key'),
        ]);
    }
}
