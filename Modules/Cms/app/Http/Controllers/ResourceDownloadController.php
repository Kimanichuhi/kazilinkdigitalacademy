<?php

namespace Modules\Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Cms\Contracts\ResourceLookupContract;
use Modules\Cms\Models\Purchase;
use Modules\Cms\Models\Resource;
use Symfony\Component\HttpFoundation\Response;

class ResourceDownloadController extends Controller
{
    public function download(string $resource, ResourceLookupContract $resources): RedirectResponse
    {
        $model = Resource::where('id', $resource)->where('is_published', true)->first();

        abort_if(! $model || ! $model->file_url, Response::HTTP_NOT_FOUND);

        if ($model->is_paid && ! $this->hasAccess($model)) {
            abort(Response::HTTP_FORBIDDEN, 'This is a premium resource. Please purchase it to download.');
        }

        $resources->incrementDownloads($model->id);

        return redirect()->away($model->file_url);
    }

    protected function hasAccess(Resource $resource): bool
    {
        return auth()->check() && Purchase::query()
            ->where('resource_id', $resource->id)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->exists();
    }
}
