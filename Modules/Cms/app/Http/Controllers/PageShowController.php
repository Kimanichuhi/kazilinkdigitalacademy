<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Cms\Contracts\PageLookupContract;
use Symfony\Component\HttpFoundation\Response;

class PageShowController extends Controller
{
    public function show(string $slug, PageLookupContract $pages): View
    {
        $page = $pages->findBySlug($slug);

        abort_if(! $page, Response::HTTP_NOT_FOUND);

        return view('cms::pages.generic', [
            'page' => $page,
            'blocks' => collect($page['blocks'] ?? [])->flatten(1),
        ]);
    }
}
