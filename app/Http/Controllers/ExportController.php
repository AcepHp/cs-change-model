<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LogDetailCs;
use App\Models\LogCs;
use App\Models\ChangeModel;
use App\Models\PartModel; // Added PartModel import
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
        
        $models = PartModel::select('Model', 'frontView')
            ->whereNotNull('frontView')
            ->whereNotNull('Model')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->Model => $item->frontView]; // Map model to frontView
            });
        
        $title = 'Export Data Checksheet';
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/dashboard', 'active' => false],
            ['label' => 'Export Data', 'url' => route('export.index'), 'active' => true],
        ];

        // Get preview data if filters are applied
        $previewData = null;
        $totalRecords = 0;
        
        if ($request->hasAny(['area', 'line', 'model', 'date', 'shift'])) {
            $query = LogDetailCs::with(['log', 'log.partModelRelation'])
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
                ->when($request->model, function ($query, $model) {
                    $query->whereHas('log', function ($q) use ($model) {
                        $q->where('model', $model);
                    });
                })
                ->when($request->date, function ($query, $date) {
                    $query->whereHas('log', function ($q) use ($date) {
                        $q->whereDate('date', $date);
                    });
                })
                ->when($request->shift, function ($query, $shift) {
                    $query->whereHas('log', function ($q) use ($shift) {
                        $q->where('shift', $shift);
                    });
                })
                ->orderBy('created_at', 'desc');

            $totalRecords = $query->count();
            $previewData = $query->limit(10)->get();
        }

        return view('export.index', compact(
            'areas', 'lines', 'models', 'title', 'breadcrumbs',
            'previewData', 'totalRecords'
        ));
    }

    public function exportExcel(Request $request)
    {
        try {
            // Set memory limit and execution time for large exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filters = $request->only([
                'area', 'line', 'model', 'date', 'shift'
            ]);

            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return !empty($value);
            });

            // Log the export attempt
            Log::info('Excel export started', ['filters' => $filters, 'user_id' => auth()->id()]);

            $query = LogDetailCs::with(['log', 'log.partModelRelation'])
                ->when($filters['area'] ?? null, function ($query, $area) {
                    $query->whereHas('log', function ($q) use ($area) {
                        $q->where('area', $area);
                    });
                })
                ->when($filters['line'] ?? null, function ($query, $line) {
                    $query->whereHas('log', function ($q) use ($line) {
                        $q->where('line', $line);
                    });
                })
                ->when($filters['model'] ?? null, function ($query, $model) {
                    $query->whereHas('log', function ($q) use ($model) {
                        $q->where('model', $model);
                    });
                })
                ->when($filters['date'] ?? null, function ($query, $date) {
                    $query->whereHas('log', function ($q) use ($date) {
                        $q->whereDate('date', $date);
                    });
                })
                ->when($filters['shift'] ?? null, function ($query, $shift) {
                    $query->whereHas('log', function ($q) use ($shift) {
                        $q->where('shift', $shift);
                    });
                });

            $count = $query->count();
            Log::info('Data count for export', ['count' => $count]);

            if ($count == 0) {
                return back()->with('error', 'Tidak ada data untuk diekspor dengan filter yang dipilih.');
            }

            // Generate filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "AVI_Checksheet_Data_{$timestamp}.xlsx";
            
            Log::info('Starting Excel download', ['filename' => $filename, 'record_count' => $count]);
            
            // Create and download Excel file
            return Excel::download(new ChecksheetExport($filters), $filename);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            Log::error('Excel validation failed', [
                'error' => $e->getMessage(),
                'failures' => $e->failures()
            ]);
            
            return back()->with('error', 'Validasi data gagal: ' . $e->getMessage());
            
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            Log::error('PhpSpreadsheet error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error dalam pembuatan file Excel: ' . $e->getMessage());
            
        } catch (Exception $e) {
            Log::error('Excel export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $filters ?? []
            ]);
            
            return back()->with('error', 'Gagal mengekspor data Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->only([
                'area', 'line', 'model', 'date', 'shift'
            ]);

            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return !empty($value);
            });

            $query = LogDetailCs::with(['log', 'log.partModelRelation'])
                ->when($filters['area'] ?? null, function ($query, $area) {
                    $query->whereHas('log', function ($q) use ($area) {
                        $q->where('area', $area);
                    });
                })
                ->when($filters['line'] ?? null, function ($query, $line) {
                    $query->whereHas('log', function ($q) use ($line) {
                        $q->where('line', $line);
                    });
                })
                ->when($filters['model'] ?? null, function ($query, $model) {
                    $query->whereHas('log', function ($q) use ($model) {
                        $q->where('model', $model);
                    });
                })
                ->when($filters['date'] ?? null, function ($query, $date) {
                    $query->whereHas('log', function ($q) use ($date) {
                        $q->whereDate('date', $date);
                    });
                })
                ->when($filters['shift'] ?? null, function ($query, $shift) {
                    $query->whereHas('log', function ($q) use ($shift) {
                        $q->where('shift', $shift);
                    });
                })
                ->orderBy(LogCs::select('date')
                    ->whereColumn('log_cs.id_log', 'log_detail_cs.id_log'), 'asc')
                ->orderBy(LogCs::select('shift')
                    ->whereColumn('log_cs.id_log', 'log_detail_cs.id_log'), 'asc')
                ->orderBy(LogCs::select('area')
                    ->whereColumn('log_cs.id_log', 'log_detail_cs.id_log'), 'asc')
                ->orderBy(LogCs::select('line')
                    ->whereColumn('log_cs.id_log', 'log_detail_cs.id_log'), 'asc')
                ->orderBy(LogCs::select('model')
                    ->whereColumn('log_cs.id_log', 'log_detail_cs.id_log'), 'asc')
                ->orderBy('list', 'asc');

            $data = $query->get();
            $totalRecords = $data->count();

            if ($totalRecords == 0) {
                return back()->with('error', 'Tidak ada data untuk diekspor dengan filter yang dipilih.');
            }

            // Check if logo exists, if not create a placeholder
            $logoPath = public_path('assets/images/AVI.png');
            if (!file_exists($logoPath)) {
                // Create images directory if it doesn't exist
                if (!file_exists(public_path('images'))) {
                    mkdir(public_path('images'), 0755, true);
                }
                
                // You can either copy an actual logo here or create a simple placeholder
                Log::warning('AVI logo not found at: ' . $logoPath);
            }

            $pdf = Pdf::loadView('export.pdf', compact('data', 'filters', 'totalRecords'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'debugKeepTemp' => false,
                    'debugCss' => false,
                    'debugLayout' => false,
                    'debugLayoutLines' => false,
                    'debugLayoutBlocks' => false,
                    'debugLayoutInline' => false,
                    'debugLayoutPaddingBox' => false,
                ]);

            $filename = 'AVI_Checksheet_Report_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (Exception $e) {
            Log::error('PDF export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
