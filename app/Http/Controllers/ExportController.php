<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\LogDetailCs;
use App\Models\LogCs;
use App\Models\ChangeModel;
use App\Models\PartModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // Added Storage facade import

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
                // Return Model as key and frontView as display value
                return [$item->Model => $item->frontView];
            });
        
        $title = 'Export Data Checksheet';
        $breadcrumbs = [
            ['label' => 'Home', 'url' => '/dashboard', 'active' => false],
            ['label' => 'Export Data', 'url' => route('export.index'), 'active' => true],
        ];

        return view('export.index', compact(
            'areas', 'lines', 'models', 'title', 'breadcrumbs'
        ));
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

            $query = LogDetailCs::with(['log' => function($query) {
                    $query->with('partModelRelation');
                }])
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
                ->join('log_cs', 'log_detail_cs.id_log', '=', 'log_cs.id_log')
                ->orderBy('log_cs.date', 'asc')
                ->orderBy('log_cs.shift', 'asc')
                ->orderBy('log_cs.area', 'asc')
                ->orderBy('log_cs.line', 'asc')
                ->orderBy('log_cs.model', 'asc')
                ->orderBy('log_detail_cs.list', 'asc')
                ->select('log_detail_cs.*');

            $data = $query->get();
            $totalRecords = $data->count();

            if ($totalRecords == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data untuk diekspor dengan filter yang dipilih.'
                ], 404);
            }

            $logoBase64 = null;
            $logoPath = public_path('assets/images/AVI.png');
            if (file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            } else {
                \Log::warning('AVI logo not found at: ' . $logoPath);
            }

            $processedData = $data->map(function ($item) {
                // Process check_item image
                if ($item->check_item) {
                    $item->check_item_base64 = $this->getImageAsBase64($item->check_item);
                }
                
                // Process result image
                if ($item->resultImage) {
                    $item->result_image_base64 = $this->getImageAsBase64($item->resultImage);
                }
                
                return $item;
            });

            dd($processedData->first());

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('export.pdf', compact('processedData', 'filters', 'totalRecords', 'logoBase64'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(), // Set chroot for better file access
                ]);

            $filename = 'AVI_Checksheet_Report_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('PDF export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getImageAsBase64($imagePath)
    {
        try {
            // Hapus "storage/" di depan kalau ada
            $normalizedPath = preg_replace('/^storage\//', '', ltrim($imagePath, '/'));

            // Full path di public/storage/
            $publicPath = public_path('storage/' . $normalizedPath);
            if (file_exists($publicPath)) {
                $file = file_get_contents($publicPath);
                $mimeType = mime_content_type($publicPath);
                return 'data:' . $mimeType . ';base64,' . base64_encode($file);
            }

            // Fallback ke Storage facade
            if (Storage::disk('public')->exists($normalizedPath)) {
                $file = Storage::disk('public')->get($normalizedPath);
                $mimeType = Storage::disk('public')->mimeType($normalizedPath);
                return 'data:' . $mimeType . ';base64,' . base64_encode($file);
            }

            \Log::warning('Image not found', [
                'original' => $imagePath,
                'normalized' => $normalizedPath,
                'publicPath' => $publicPath
            ]);
            return null;

        } catch (\Exception $e) {
            \Log::error('Failed to convert image to base64', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }


}
