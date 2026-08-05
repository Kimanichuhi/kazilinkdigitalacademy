<?php

namespace Modules\Academy\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academy\Contracts\CohortLookupContract;
use Modules\Academy\Contracts\ProgramLookupContract;
use Symfony\Component\HttpFoundation\Response;

class ProgramController extends Controller
{
    public function index(Request $request, ProgramLookupContract $programs): JsonResponse
    {
        $result = $programs->listPublishedPaginated([
            'category_id' => $request->query('category_id'),
            'level' => $request->query('level'),
            'delivery_mode' => $request->query('delivery_mode'),
            'search' => $request->query('search'),
            'sort' => $request->query('sort'),
        ], min((int) $request->query('per_page', 12), 50));

        return response()->json($result);
    }

    public function show(string $slug, ProgramLookupContract $programs): JsonResponse
    {
        $program = $programs->findBySlug($slug);

        abort_if(! $program, Response::HTTP_NOT_FOUND);

        return response()->json(['data' => $program]);
    }

    public function cohorts(string $slug, ProgramLookupContract $programs, CohortLookupContract $cohorts): JsonResponse
    {
        $program = $programs->findBySlug($slug);

        abort_if(! $program, Response::HTTP_NOT_FOUND);

        return response()->json(['data' => $cohorts->openForProgram($program['id'])]);
    }
}
