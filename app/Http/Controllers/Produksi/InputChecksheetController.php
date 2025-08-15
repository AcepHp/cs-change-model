<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChangeModel;
use Carbon\Carbon;
use App\Models\LogCs;
use App\Models\LogDetailCs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Support\Arr;
use App\Models\PartModel;


class InputChecksheetController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $areas = ChangeModel::select('area')->distinct()->orderBy('area')->pluck('area');
        $lines = ChangeModel::select('line')->distinct()->orderBy('line')->pluck('line');
        $models = ChangeModel::select('model')->distinct()->orderBy('model')->pluck('model');

        return view('produksi.cs.filter', [
            'title' => 'Filter Data Checksheet',
            'areas' => $areas,
            'lines' => $lines,
            'models' => $models,
            'today' => $today,
            'tomorrow' => $tomorrow
        ]);
    }

    public function filter(Request $request)
    {
        $shift = $request->shift;
        $date = $request->date;
        $area = $request->area;
        $line = $request->line;
        $model = $request->model;

        $query = ChangeModel::query();

        if ($area) $query->where('area', $area);
        if ($line) $query->where('line', $line);
        if ($model) $query->where('model', $model);

        $results = $query->get();

        $frontView = PartModel::where('Model', $model)->value('frontView');

        // Cek apakah data sudah di-submit
        $log = LogCs::where('area', $area)
            ->where('line', $line)
            ->where('model', $model)
            ->where('shift', $shift)
            ->where('date', $date)
            ->first();

        $totalItems = $query->count();
        $submittedItems = $log ? LogDetailCs::where('id_log', $log->id_log)->count() : 0;
        $isSubmitted = $totalItems > 0 && $submittedItems === $totalItems;

        $id_log = $log ? $log->id_log : null;
        
        // Fetch existing log details to pre-fill the table
        $existingLogDetails = [];
        if ($id_log) {
            $existingLogDetails = LogDetailCs::where('id_log', $id_log)
                                            ->get()
                                            ->keyBy(function($item) {
                                                // Create a unique key based on check_item and standard
                                                return $item->check_item . '|' . $item->standard;
                                            });
        }

        return view('produksi.cs.result', [
            'title' => 'Hasil Filter Checksheet',
            'results' => $results,
            'shift' => $shift,
            'date' => $date,
            'area' => $area,
            'line' => $line,
            'model' => $model,
            'frontView' => $frontView,
            'isSubmitted' => $isSubmitted,
            'id_log' => $id_log,
            'existingLogDetails' => $existingLogDetails // Pass existing details
        ]);
    }

    public function saveChecksheetResult(Request $request)
    {
        Log::info('Save checksheet request:', $request->all());
        
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'area' => 'required|string',
                'line' => 'required|string',
                'model' => 'required|string',
                'station' => 'required|string',
                'shift' => 'required|string',
                'date' => 'required|date',
                'item_id' => 'required|integer',
                'scan_result' => 'nullable|string',
                'production_status' => 'required|string|in:OK,NG,N/A',
                'actual' => 'nullable|string',
                'list' => 'nullable|string',
                'image' => 'nullable|string', // Base64 image data
                'image_type' => 'nullable|string', // image_type from ChangeModel (for validation/context)
                'check_item' => 'required|string', // Added for finding ChangeModel
                'standard' => 'required|string', // Added for finding ChangeModel
            ]);

            // Validasi: Jika status NG, tidak boleh submit
            if ($validated['production_status'] === 'NG') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status NG tidak dapat disubmit. Silakan perbaiki terlebih dahulu.'
                ], 400);
            }

            Log::info('Validation passed:', $validated);

            // Cari atau buat LogCs
            $logCs = LogCs::firstOrCreate([
                'area' => $validated['area'],
                'line' => $validated['line'],
                'model' => $validated['model'],
                'shift' => $validated['shift'],
                'date' => $validated['date']
            ]);

            Log::info('LogCs ID:', ['id' => $logCs->id_log]);

            $uploadedImagePath = null;

            // Handle image upload if present for THIS specific item
            if ($request->has('image') && !empty($request->image)) {
                $imageData = $validated['image'];

                // Pecah base64, ambil bagian setelah "data:image/jpeg;base64,"
                $base64Image = explode(',', $imageData)[1] ?? null;

                if ($base64Image) {
                    $decodedImage = base64_decode($base64Image);

                    $fileName = 'checksheet_image_' . uniqid() . '.jpeg';
                    $filePath = 'checksheet-image/' . $fileName;

                    // Simpan ke storage/app/public/checksheet-image/
                    Storage::disk('public')->put($filePath, $decodedImage);

                    // Buat URL publik (untuk <img src="...">)
                    $uploadedImagePath = asset('storage/' . $filePath);

                    Log::info('Image uploaded for specific item:', [
                        'path' => $uploadedImagePath,
                        'item_id' => $validated['item_id']
                    ]);
                } else {
                    Log::warning('Base64 image data invalid or missing.');
                }
            }

            // Get checksheet item (the one that triggered the save)
            $checksheetItem = ChangeModel::find($validated['item_id']);
            
            if (!$checksheetItem) {
                Log::error('Checksheet item not found:', ['item_id' => $validated['item_id']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Item checksheet tidak ditemukan'
                ], 404);
            }

            Log::info('Checksheet item found:', $checksheetItem->toArray());

            $scanResult = $validated['scan_result'] ?? $validated['actual'] ?? '';

            // Find or create the specific logDetail for the current item being submitted
            $logDetail = LogDetailCs::firstOrNew([
                'id_log' => $logCs->id_log,
                'station' => $validated['station'],
                'check_item' => $checksheetItem->check_item,
                'standard' => $checksheetItem->standard,
            ]);

            // Update fields for the specific item
            $logDetail->scanResult = $scanResult;
            $logDetail->list = $validated['list'] ?? $checksheetItem->list;
            $logDetail->prod_status = $validated['production_status'];
            $logDetail->prod_checked_by = Auth::check() ? Auth::user()->name : 'Produksi';
            $logDetail->prod_checked_at = now();
            $logDetail->updated_at = now();

            // Set resultImage ONLY for this specific item if an image was uploaded for it
            if ($uploadedImagePath) {
                $logDetail->resultImage = $uploadedImagePath;
            }
            // If no new image is uploaded (empty string or null), and there was an existing one, keep it.
            // If an empty string is sent for 'image', it means the user cleared it or didn't take one.
            // If the user wants to clear an image, we'd need a separate mechanism.
            // For now, if image is empty string, and there was no existing image, resultImage remains null.
            // If image is empty string, and there was an existing image, it remains.

            $logDetail->save(); // Save or update the specific item
            
            Log::info('Specific detail saved/updated:', ['id' => $logDetail->id_det]);

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => [
                    'log_id' => $logCs->id_log,
                    'detail_id' => $logDetail->id_det,
                    'scan_result' => $scanResult,
                    'list' => $logDetail->list,
                    'status' => $validated['production_status'],
                    'checked_by' => $logDetail->prod_checked_by,
                    'checked_at' => $logDetail->prod_checked_at->format('Y-m-d H:i:s'),
                    'is_new_record' => !$logDetail->wasRecentlyCreated,
                    'image_url' => $uploadedImagePath, // Return the uploaded image URL for this item
                ]
            ];

            Log::info('Success response:', $response);
            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Validation error:', $e->errors());
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', Arr::flatten($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Save checksheet error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
