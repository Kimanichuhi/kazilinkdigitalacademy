<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * index() is the guarded /admin dashboard shell. Counts are read via
     * plain DB::table() queries rather than Contracts — the dashboard is a
     * cross-cutting reporting view over every module's data, not a business
     * action, so a lightweight direct COUNT is a pragmatic exception to the
     * "no cross-module Model imports" rule (no Eloquent Model is imported).
     * Full charts/analytics are built in Phase 7.
     */
    public function index(Request $request): View
    {
        $stats = [
            ['label' => 'Total Bookings', 'value' => DB::table('bookings')->count()],
            ['label' => 'Total Students', 'value' => DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', 'student')->count()],
            ['label' => 'Published Programs', 'value' => DB::table('programs')->where('is_published', true)->count()],
            ['label' => 'Active Cohorts', 'value' => DB::table('cohorts')->whereIn('status', ['open', 'upcoming'])->count()],
            ['label' => 'Total Resources', 'value' => DB::table('resources')->count()],
            ['label' => 'Free Resources', 'value' => DB::table('resources')->where('is_paid', false)->count()],
            ['label' => 'Premium Resources', 'value' => DB::table('resources')->where('is_paid', true)->count()],
            ['label' => 'Resource Purchases', 'value' => DB::table('purchases')->where('status', 'paid')->count()],
            ['label' => 'Resource Revenue', 'value' => 'KES '.number_format((float) DB::table('purchases')->where('status', 'paid')->sum('amount'))],
        ];

        $topResources = DB::table('resources')
            ->orderByDesc('download_count')
            ->limit(5)
            ->select('title', 'download_count')
            ->get();

        return view('admin::dashboard', ['user' => $request->user(), 'stats' => $stats, 'topResources' => $topResources]);
    }

    /**
     * Reports across county/constituency/program/revenue/resource sales —
     * same "raw DB::table(), no Eloquent Model" exception as index() above,
     * since this is cross-cutting reporting rather than a business action.
     */
    public function reports(): View
    {
        $byCounty = DB::table('bookings')
            ->whereNotNull('county')
            ->select('county', DB::raw('count(*) as total'))
            ->groupBy('county')
            ->orderByDesc('total')
            ->get();

        $byConstituency = DB::table('bookings')
            ->whereNotNull('constituency')
            ->select('constituency', DB::raw('count(*) as total'))
            ->groupBy('constituency')
            ->orderByDesc('total')
            ->get();

        $byProgram = DB::table('bookings')
            ->join('programs', 'programs.id', '=', 'bookings.program_id')
            ->select('programs.title', DB::raw('count(*) as total'), DB::raw('sum(bookings.amount_paid) as revenue'))
            ->groupBy('programs.id', 'programs.title')
            ->orderByDesc('total')
            ->get();

        $resourceSales = DB::table('purchases')
            ->join('resources', 'resources.id', '=', 'purchases.resource_id')
            ->where('purchases.status', 'paid')
            ->select('resources.title', DB::raw('count(*) as total'), DB::raw('sum(purchases.amount) as revenue'))
            ->groupBy('resources.id', 'resources.title')
            ->orderByDesc('total')
            ->get();

        return view('admin::reports', [
            'byCounty' => $byCounty,
            'byConstituency' => $byConstituency,
            'byProgram' => $byProgram,
            'bookingRevenue' => (float) DB::table('bookings')->sum('amount_paid'),
            'resourceSales' => $resourceSales,
            'resourceRevenue' => (float) DB::table('purchases')->where('status', 'paid')->sum('amount'),
        ]);
    }

    /**
     * Analytics port of app/admin/analytics/page.tsx: only the 3 top stat
     * tiles are computed from real data (matches source exactly — it fetches
     * every booking's total_amount/amount_paid client-side with no date
     * filtering). Both charts, the pie breakdown, and the Key Metrics panel
     * are hardcoded/mock in the source (confirmed in MIGRATION-INVENTORY.md)
     * and are reproduced as the same static arrays here, just rendered with
     * ApexCharts instead of recharts per the target stack.
     */
    public function analytics(): View
    {
        $totalBookings = DB::table('bookings')->count();
        $totalRevenue = (float) DB::table('bookings')->sum('amount_paid');
        $avgValue = $totalBookings > 0 ? round($totalRevenue / $totalBookings) : 0;

        $monthly = [
            ['month' => 'Jan', 'bookings' => 24, 'revenue' => 360000],
            ['month' => 'Feb', 'bookings' => 31, 'revenue' => 465000],
            ['month' => 'Mar', 'bookings' => 38, 'revenue' => 570000],
            ['month' => 'Apr', 'bookings' => 45, 'revenue' => 675000],
            ['month' => 'May', 'bookings' => 52, 'revenue' => 780000],
            ['month' => 'Jun', 'bookings' => 59, 'revenue' => 885000],
            ['month' => 'Jul', 'bookings' => 67, 'revenue' => 1005000],
        ];

        $programPopularity = [
            ['name' => 'Freelancing', 'value' => 35, 'color' => '#4CAF50'],
            ['name' => 'E-Commerce', 'value' => 22, 'color' => '#f97316'],
            ['name' => 'Digital Mktg', 'value' => 20, 'color' => '#10b981'],
            ['name' => 'Full-Stack Dev', 'value' => 15, 'color' => '#8b5cf6'],
            ['name' => 'Others', 'value' => 8, 'color' => '#6b7280'],
        ];

        $keyMetrics = [
            ['label' => 'Conversion Rate', 'value' => '34%', 'description' => 'Visitors who book'],
            ['label' => 'Avg Time to Approve', 'value' => '18h', 'description' => 'From booking to approval'],
            ['label' => 'Returning Students', 'value' => '28%', 'description' => 'Enrolled in 2+ programs'],
            ['label' => 'Referral Rate', 'value' => '42%', 'description' => 'Bookings from word of mouth'],
        ];

        return view('admin::analytics', [
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'avgValue' => $avgValue,
            'monthly' => $monthly,
            'programPopularity' => $programPopularity,
            'keyMetrics' => $keyMetrics,
        ]);
    }
}
