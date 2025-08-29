<?php

namespace App\Http\Controllers;

use App\Models\LogDetailCs;
use App\Models\LogCs;
use App\Models\PartModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function index()
    {
        $areas = LogCs::distinct()->pluck('area')->filter()->sort()->values();
        $lines = LogCs::distinct()->pluck('line')->filter()->sort()->values();
        $models = PartModel::pluck('frontView', 'Model')->toArray();

        return view('export.index', [
            'title' => 'Export Data',
            'areas' => $areas,
            'lines' => $lines,
            'models' => $models,
        ]);
    }

    public function exportPdf(Request $request)
    {
        try {
            Log::info('PDF Export started with filters', $request->all());

            $query = LogDetailCs::with(['log.partModelRelation']);

            if ($request->filled('area')) {
                $query->whereHas('log', function ($q) use ($request) {
                    $q->where('area', $request->area);
                });
            }

            if ($request->filled('line')) {
                $query->whereHas('log', function ($q) use ($request) {
                    $q->where('line', $request->line);
                });
            }

            if ($request->filled('model')) {
                $query->whereHas('log', function ($q) use ($request) {
                    $q->where('model', $request->model);
                });
            }

            if ($request->filled('date')) {
                $query->whereHas('log', function ($q) use ($request) {
                    $q->whereDate('date', $request->date);
                });
            }

            if ($request->filled('shift')) {
                $query->whereHas('log', function ($q) use ($request) {
                    $q->where('shift', $request->shift);
                });
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'No data found'], 404);
            }

            $processedData = $this->processDataForPdf($data);
            $logoBase64 = $this->getLogoBase64();
            $filters = $request->only(['date', 'shift', 'area', 'line', 'model']);

            $pdf = Pdf::loadView('export.pdf', [
                'processedData' => $processedData,
                'totalRecords' => $data->count(),
                'filters' => $filters,
                'logoBase64' => $logoBase64,
            ]);

            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial',
                'dpi' => 150,
            ]);

            $filename = 'AVI_Checksheet_Report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('PDF Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processDataForPdf($data)
    {
        return $data->map(function ($item) {
            $item->check_item_base64 = null;
            if ($item->check_item && $this->isImagePath($item->check_item)) {
                $item->check_item_base64 = $this->convertImageToBase64($item->check_item);
            }

            $item->result_image_base64 = null;
            if ($item->resultImage && $this->isImagePath($item->resultImage)) {
                $item->result_image_base64 = $this->convertImageToBase64($item->resultImage);
            }

            return $item;
        });
    }

    private function isImagePath($path)
    {
        if (empty($path)) {
            return false;
        }

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return in_array($extension, $imageExtensions);
    }

    private function convertImageToBase64($imagePath)
    {
        try {
            $imagePath = ltrim($imagePath, '/');
            
            $possiblePaths = [
                'public/storage/.' . $imagePath,
                $imagePath,
                'public/' . $imagePath,
                'app/public/' . $imagePath,
                'storage/app/public/' . $imagePath,
            ];

            $imageData = null;
            $actualPath = null;

            foreach ($possiblePaths as $path) {
                if (Storage::exists($path)) {
                    $imageData = Storage::get($path);
                    $actualPath = $path;
                    break;
                }
                
                $fullPath = storage_path('app/' . $path);
                if (file_exists($fullPath)) {
                    $imageData = file_get_contents($fullPath);
                    $actualPath = $fullPath;
                    break;
                }
            }

            if (!$imageData) {
                Log::warning('Image not found for PDF', ['path' => $imagePath]);
                return null;
            }

            $mimeType = $this->getMimeType($imagePath, $imageData);
            $base64 = base64_encode($imageData);
            
            return "data:{$mimeType};base64,{$base64}";

        } catch (\Exception $e) {
            Log::error('Failed to convert image to base64', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function getMimeType($imagePath, $imageData = null)
    {
        $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];

        if (isset($mimeTypes[$extension])) {
            return $mimeTypes[$extension];
        }

        if ($imageData) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->buffer($imageData);
            if ($detectedMime && strpos($detectedMime, 'image/') === 0) {
                return $detectedMime;
            }
        }

        return 'image/jpeg';
    }

    private function getLogoBase64()
    {
        try {
            $paths = [
                public_path('assets/images/AVI.png'),
                public_path('assets/logo.png'),
                public_path('images/logo.png'),
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $logoData = file_get_contents($path);
                    $mimeType = mime_content_type($path);
                    return "data:{$mimeType};base64," . base64_encode($logoData);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not load logo for PDF', ['error' => $e->getMessage()]);
        }

        return null;
    }

    public function exportExcel(Request $request)
    {
        // no code:(
    }
}
