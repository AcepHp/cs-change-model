<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LogDetailCs;
use App\Models\LogCs;
use App\Models\ChangeModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ChecksheetExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        // Get filter options
        $areas = \DB::table('log_cs')->select('area')->distinct()->whereNotNull('area')->pluck('area');
        $lines = \DB::table('log_cs')->select('line')->distinct()->whereNotNull('line')->pluck('line');
        $models = ChangeModel::select('model')->distinct()->whereNotNull('model')->pluck('model');

        $title = 'Export Data Checksheet';
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/dashboard', 'active' => false],
            ['label' => 'Export Data', 'url' => route('export.index'), 'active' => true],
        ];

        // Get preview data if filters are applied
        $previewData = null;
        $totalRecords = 0;

        if ($request->hasAny(['area', 'line', 'model', 'date', 'shift'])) {
            $query = LogDetailCs::with('log')
                ->when($request->area, fn($query, $area) => $query->whereHas('log', fn($q) => $q->where('area', $area)))
                ->when($request->line, fn($query, $line) => $query->whereHas('log', fn($q) => $q->where('line', $line)))
                ->when($request->model, fn($query, $model) => $query->whereHas('log', fn($q) => $q->where('model', $model)))
                ->when($request->date, fn($query, $date) => $query->whereHas('log', fn($q) => $q->whereDate('date', $date)))
                ->when($request->shift, fn($query, $shift) => $query->whereHas('log', fn($q) => $q->where('shift', $shift)))
                ->orderBy('created_at', 'desc');

            $totalRecords = $query->count();
            $previewData = $query->limit(10)->get();
        }

        return view('export.index', compact(
            'areas', 'lines', 'models', 'title', 'breadcrumbs',
            'previewData', 'totalRecords'
        ));
    }

    public function exportPdf(Request $request)
    {
        try {
            // Validate required fields
            $request->validate([
                'date' => 'required|date',
                'shift' => 'required|in:1,2',
                'area' => 'required|string',
                'line' => 'required|string',
                'model' => 'required|string',
            ], [
                'date.required' => 'Tanggal wajib diisi',
                'shift.required' => 'Shift wajib dipilih',
                'area.required' => 'Area wajib dipilih',
                'line.required' => 'Line wajib dipilih',
                'model.required' => 'Model wajib dipilih',
            ]);

            // Set memory limit and execution time for large exports
            ini_set('memory_limit', '512M');
            set_time_limit(2000); // 5 minutes

            $filters = $request->only(['area', 'line', 'model', 'date', 'shift']);

            // Log the export attempt
            Log::info('PDF export started', ['filters' => $filters, 'user_id' => auth()->id()]);

            $query = LogDetailCs::with('log')
                ->when($filters['area'], fn($query, $area) => $query->whereHas('log', fn($q) => $q->where('area', $area)))
                ->when($filters['line'], fn($query, $line) => $query->whereHas('log', fn($q) => $q->where('line', $line)))
                ->when($filters['model'], fn($query, $model) => $query->whereHas('log', fn($q) => $q->where('model', $model)))
                ->when($filters['date'], fn($query, $date) => $query->whereHas('log', fn($q) => $q->whereDate('date', $date)))
                ->when($filters['shift'], fn($query, $shift) => $query->whereHas('log', fn($q) => $q->where('shift', $shift)))
                ->orderBy('list', 'asc');

            $data = $query->get();
            $totalRecords = $data->count();

            Log::info('Data count for PDF export', ['count' => $totalRecords]);

            if ($totalRecords == 0) {
                return back()->with('error', 'Tidak ada data untuk diekspor dengan filter yang dipilih.');
            }

            // Create PDF with improved settings
            $pdf = Pdf::loadView('export.pdf', compact('data', 'filters', 'totalRecords'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'debugPng' => false,
                    'debugKeepTemp' => false,
                    'debugCss' => false,
                    'chroot' => public_path(),
                ]);

            $filename = 'AVI_Checksheet_Report_' . date('Y-m-d_H-i-s') . '.pdf';
            
            Log::info('PDF export completed successfully', ['filename' => $filename, 'record_count' => $totalRecords]);
            
            return $pdf->download($filename);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
            
        } catch (Exception $e) {
            Log::error('PDF export failed', [
                'error' => $e->getMessage(), 
                'trace' => $e->getTraceAsString(),
                'filters' => $filters ?? []
            ]);
            
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
