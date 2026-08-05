<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Modules\Cms\Contracts\BlogLookupContract;
use Symfony\Component\HttpFoundation\Response;

class BlogShowController extends Controller
{
    public function show(string $slug, BlogLookupContract $blog): View
    {
        $post = $blog->findBySlug($slug);

        abort_if(! $post, Response::HTTP_NOT_FOUND);

        $blog->incrementViewCount($post['id']);

        return view('cms::blog.show', [
            'post' => $post,
            'related' => collect($blog->listPublished())
                ->reject(fn ($p) => $p['id'] === $post['id'])
                ->when($post['category_id'], fn ($items) => $items->where('category_id', $post['category_id']))
                ->take(3)
                ->values(),
        ]);
    }
}
