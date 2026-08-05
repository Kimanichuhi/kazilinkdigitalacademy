<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Academy\Contracts\TrainerLookupContract;
use Modules\Cms\Contracts\SiteSettingLookupContract;
use Modules\Cms\Contracts\TestimonialLookupContract;
use Modules\Core\Support\OptionalContract;
use Modules\Marketing\Contracts\AdvertisementLookupContract;
use Modules\Marketing\Contracts\CtaLookupContract;
use Modules\Marketing\Contracts\StatisticLookupContract;

class HomeController extends Controller
{
    /**
     * Academy is this app's core domain (a booking site with no programs
     * makes no sense), so its Contracts are injected normally and are
     * allowed to fail loudly if that module is ever disabled. Cms/Marketing
     * are content/decoration layers over the home page — resolved via
     * OptionalContract so disabling either one degrades the relevant
     * section (no testimonials, no stats bar, no ads/CTA) instead of
     * 500ing the entire page.
     */
    public function index(
        ProgramLookupContract $programs,
        CohortLookupContract $cohorts,
        TrainerLookupContract $trainers,
    ): View {
        $settings = OptionalContract::resolve(SiteSettingLookupContract::class);
        $testimonials = OptionalContract::resolve(TestimonialLookupContract::class);
        $statistics = OptionalContract::resolve(StatisticLookupContract::class);
        $ctas = OptionalContract::resolve(CtaLookupContract::class);
        $advertisements = OptionalContract::resolve(AdvertisementLookupContract::class);

        $stats = $statistics?->listActive() ?? [];

        return view('core::home', [
            'settings' => $settings?->getMany([
                'hero_title', 'hero_subtitle', 'hero_cta_primary', 'hero_cta_secondary', 'hero_image_url', 'site_name',
            ]) ?? [],
            'programs' => $programs->listFeatured(6),
            'cohorts' => $cohorts->upcomingAcrossPrograms(4),
            'testimonials' => $testimonials?->listFeatured(6) ?? [],
            'trainers' => $trainers->listFeatured(4),
            'stats' => $stats,
            'statsByKey' => collect($stats)->filter(fn ($s) => $s['key'])->keyBy('key'),
            'cta' => $ctas?->activeForPlacement('homepage'),
            'ads' => $advertisements?->listActiveForPlacement('homepage', 5) ?? [],
        ]);
    }
}
