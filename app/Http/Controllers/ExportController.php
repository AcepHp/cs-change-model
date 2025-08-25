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
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $areas = \DB::table('log_cs')->select('area')->distinct()->whereNotNull('area')->pluck('area');
        $lines = \DB::table('log_cs')->select('line')->distinct()->whereNotNull('line')->pluck('line');
        
        $models = PartModel::select('Model', 'frontView')
            ->whereNotNull('frontView')
            ->whereNotNull('Model')
            ->distinct()
            ->get()
            ->mapWithKeys(function ($item) {
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

            $filters = array_filter($filters, fn($value) => !empty($value));

            $query = LogDetailCs::with(['log' => function($query) {
                    $query->with('partModelRelation');
                }])
                ->when($filters['area'] ?? null, fn($query, $area) =>
                    $query->whereHas('log', fn($q) => $q->where('area', $area)))
                ->when($filters['line'] ?? null, fn($query, $line) =>
                    $query->whereHas('log', fn($q) => $q->where('line', $line)))
                ->when($filters['model'] ?? null, fn($query, $model) =>
                    $query->whereHas('log', fn($q) => $q->where('model', $model)))
                ->when($filters['date'] ?? null, fn($query, $date) =>
                    $query->whereHas('log', fn($q) => $q->whereDate('date', $date)))
                ->when($filters['shift'] ?? null, fn($query, $shift) =>
                    $query->whereHas('log', fn($q) => $q->where('shift', $shift)))
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

            // Logo perusahaan
            $logoBase64 = null;
            $logoPath = public_path('assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'AVI.png');
            if (file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $mimeType = $this->detectMimeType($logoPath);
                $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($logoData);
            } else {
                \Log::warning('AVI logo not found at: ' . $logoPath);
            }

            // Konversi gambar data menjadi base64
            $processedData = $data->map(function ($item) {
                if ($item->check_item) {
                    $item->check_item_base64 = $this->getImageAsBase64($item->check_item);
                }
                if ($item->resultImage) {
                    $item->result_image_base64 = $this->getImageAsBase64($item->resultImage);
                }
                return $item;
            });

            $pdf = Pdf::loadView('export.pdf', compact('processedData', 'filters', 'totalRecords', 'logoBase64'))
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => public_path(),
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

    /**
     * Convert image to base64 (support Linux & Windows).
     */
    private function getImageAsBase64($imagePath)
    {
        try {
            $normalizedPath = preg_replace('/^storage[\/\\\\]/', '', ltrim($imagePath, '/\\'));
            $publicPath = public_path('storage' . DIRECTORY_SEPARATOR . $normalizedPath);

            if (file_exists($publicPath)) {
                $file = file_get_contents($publicPath);
                $mimeType = $this->detectMimeType($publicPath);
                $base64 = base64_encode($file);
                $base64 = preg_replace('/\s+/', '', $base64); // Hapus whitespace
                return 'data:' . $mimeType . ';base64,' . $base64;
            }

            if (Storage::disk('public')->exists($normalizedPath)) {
                $file = Storage::disk('public')->get($normalizedPath);
                $mimeType = $this->detectMimeType($normalizedPath, true);
                $base64 = base64_encode($file);
                $base64 = preg_replace('/\s+/', '', $base64); // Hapus whitespace
                return 'data:' . $mimeType . ';base64,' . $base64;
            }

            \Log::warning('Image not found', [
                'original'   => $imagePath,
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


    /**
     * Detect mime type safely with fallback.
     */
    private function detectMimeType($path, $isStorage = false)
    {
        try {
            if (!$isStorage) {
                $mimeType = @mime_content_type($path);
            } else {
                $mimeType = Storage::disk('public')->mimeType($path);
            }

            if ($mimeType) return $mimeType;

            // fallback by extension
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            return match(strtolower($ext)) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                default => 'application/octet-stream',
            };
        } catch (\Exception $e) {
            return 'application/octet-stream';
        }
    }
}
