<?php

namespace Modules\Cms\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Cms\Contracts\ResourceLookupContract;
use Modules\Cms\Services\ResourcePurchaseService;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    public function index(Request $request, ResourceLookupContract $resources): JsonResponse
    {
        $result = $resources->listPublishedPaginated([
            'type' => $request->query('type'),
            'search' => $request->query('search'),
        ], min((int) $request->query('per_page', 12), 50));

        return response()->json($result);
    }

    public function show(string $id, ResourceLookupContract $resources): JsonResponse
    {
        $resource = $resources->find($id);

        abort_if(! $resource, Response::HTTP_NOT_FOUND);

        return response()->json(['data' => $resource]);
    }

    public function purchase(Request $request, string $id, ResourcePurchaseService $purchases): JsonResponse
    {
        $data = $request->validate([
            'phone' => 'required|string|min:9',
        ]);

        $result = $purchases->initiate($id, $request->user()->id, $data['phone']);

        return response()->json(['data' => $result]);
    }
}
