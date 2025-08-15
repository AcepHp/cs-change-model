<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LogDetailCs;
use App\Models\LogCs;
use App\Models\ChangeModel;
use App\Models\PartModel;

class ProduksiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $shift = $request->get('shift');

        // Data untuk tabel hari ini (dengan pagination)
        $logDetailTableData = LogDetailCs::with(['log.partModelRelation'])
            ->whereHas('log', function ($query) use ($today, $shift) {
                $query->whereDate('date', $today);
                if (!is_null($shift)) {
                    $query->where('shift', $shift);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends($request->query());

        // Data untuk tabel log utama - menggunakan frontView
        $logTableData = LogCs::select('log_cs.*', 'pm.frontView')
            ->leftJoin('part_model as pm', 'pm.Model', '=', 'log_cs.model')
            ->whereDate('log_cs.date', $today)
            ->when(!is_null($shift), function ($query) use ($shift) {
                $query->where('log_cs.shift', $shift);
            })
            ->orderBy('log_cs.date', 'desc')
            ->paginate(5);

        // Statistics
        $checksheetToday = LogDetailCs::whereHas('log', fn($q) => $q->whereDate('date', $today))->count();
        $checksheetShift1 = LogDetailCs::whereHas('log', fn($q) => $q->whereDate('date', $today)->where('shift', 1))->count();
        $checksheetShift2 = LogDetailCs::whereHas('log', fn($q) => $q->whereDate('date', $today)->where('shift', 2))->count();
        $totalCSChangeModel = ChangeModel::count();

        // Ambil semua data hari ini tanpa pagination untuk akurasi perhitungan chart
        $logDetailTodayData = LogDetailCs::whereHas('log', function ($query) use ($today, $shift) {
            $query->whereDate('date', $today);
            if (!is_null($shift)) {
                $query->where('shift', $shift);
            }
        })->get();

        // Gunakan seluruh data untuk perhitungan OK/NG
        $okCount = $logDetailTodayData->where('prod_status', 'OK')->count();
        $ngCount = $logDetailTodayData->where('prod_status', 'NG')->count();

        $qualityOkCount = $logDetailTodayData->where('quality_status', 'OK')->count();
        $qualityNgCount = $logDetailTodayData->where('quality_status', 'NG')->count();
        $qualityPendingCount = $logDetailTodayData->whereNull('quality_status')->count();

        $totalQualityValidated = LogDetailCs::whereNotNull('quality_status')
            ->whereHas('log', fn($q) => $q->whereDate('date', $today))
            ->count();
        $totalQualityOkAll = LogDetailCs::where('quality_status', 'OK')->count();
        $totalQualityNgAll = LogDetailCs::where('quality_status', 'NG')->count();
        $totalQualityValidatedToday = LogDetailCs::whereHas('log', fn($q) => $q->whereDate('date', $today))
            ->whereNotNull('quality_status')
            ->count();

        // Filter options
        $areas = \DB::table('log_cs')->select('area')->distinct()->pluck('area');
        $lines = \DB::table('log_cs')->select('line')->distinct()->pluck('line');
        
        // Ambil frontView dari tabel part_model untuk filter
        $frontViews = \DB::table('part_model')
            ->select('frontView')
            ->distinct()
            ->whereNotNull('frontView')
            ->orderBy('frontView')
            ->pluck('frontView');

        // Data untuk tabel total dengan pagination dan filter
        $totalTableQuery = LogDetailCs::with(['log.partModelRelation'])
            ->when($request->area, function ($query, $area) {
                $query->whereHas('log', function ($q) use ($area) {
                    $q->where('area', $area);
                });
            })
            ->when($request->line, function ($query, $line) {
                $query->whereHas('log', function ($q) use ($line) {
                    $q->where('line', $line);
                });
            })
            // Filter berdasarkan frontView
            ->when($request->frontview, function ($query, $frontView) {
                $query->whereHas('log.partModelRelation', function ($q) use ($frontView) {
                    $q->where('frontView', $frontView);
                });
            })
            ->when($request->shift_filter, function ($query, $shift) {
                $query->whereHas('log', function ($q) use ($shift) {
                    $q->where('shift', $shift);
                });
            })
            ->when($request->date, function ($query, $date) {
                $query->whereHas('log', function ($q) use ($date) {
                    $q->whereDate('date', $date);
                });
            })
            ->orderBy('created_at', 'desc');

        $totalTableData = $totalTableQuery->paginate(5)->appends($request->query());

        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/dashboard', 'active' => false],
            ['label' => 'Dashboard', 'url' => '/dashboard', 'active' => true],
        ];

        $title = 'Dashboard';

        // Jika request AJAX untuk filter, return hanya table
        if ($request->ajax()) {
            return view('produksi.dashboard.partials.log_detail_table', compact('totalTableData'))->render();
        }

        return view('produksi.dashboard.index', compact(
            'checksheetToday', 'checksheetShift1', 'checksheetShift2',
            'totalCSChangeModel', 'logDetailTableData', 'totalTableData', 'shift',
            'okCount', 'ngCount', 'qualityOkCount', 'qualityNgCount', 'qualityPendingCount',
            'totalQualityValidated', 'totalQualityOkAll', 'totalQualityNgAll', 'totalQualityValidatedToday',
            'areas', 'lines', 'frontViews', 'title', 'breadcrumbs', 'logTableData'
        ));
    }
}
