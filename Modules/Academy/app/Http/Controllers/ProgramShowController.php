<?php

namespace Modules\Academy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Modules\Cms\Contracts\FaqLookupContract;
use Modules\Cms\Contracts\TestimonialLookupContract;
use Modules\Core\Support\OptionalContract;
use Symfony\Component\HttpFoundation\Response;

class ProgramShowController extends Controller
{
    public function show(
        string $slug,
        ProgramLookupContract $programs,
        CohortLookupContract $cohorts,
    ): View {
        $program = $programs->findBySlug($slug);

        abort_if(! $program, Response::HTTP_NOT_FOUND);

        $related = $program['category_id']
            ? $programs->listRelated($program['category_id'], $program['id'], 3)
            : [];

        $testimonials = OptionalContract::resolve(TestimonialLookupContract::class);
        $faqs = OptionalContract::resolve(FaqLookupContract::class);

        return view('academy::programs.show', [
            'program' => $program,
            'cohorts' => $cohorts->openForProgram($program['id']),
            'testimonials' => $testimonials?->listForProgram($program['id'], 4) ?? [],
            'faqs' => $faqs?->listPublished(8) ?? [],
            'related' => $related,
        ]);
    }
}
