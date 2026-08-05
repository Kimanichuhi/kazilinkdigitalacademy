<?php

namespace Modules\Academy\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * /trainer was referenced but never built in the source
 * (see MIGRATION-INVENTORY.md §3.6) — this is the guarded empty scaffold
 * the task brief calls for, owned by Academy since it already owns the
 * Trainer domain model.
 */
class TrainerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('academy::trainer.dashboard', ['user' => $request->user()]);
    }
}
