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
        
        return view('produksi.cs.result', [
            'title' => 'Hasil Filter Checksheet',
            'results' => $results,
            'shift' => $shift,
            'date' => $date,
            'area' => $area,
            'line' => $line,
            'model' => $model,
            'isSubmitted' => $isSubmitted,
            'id_log' => $id_log
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
                'production_status' => 'required|string|in:OK,NG',
                'actual' => 'nullable|string',
                'list' => 'nullable|string' // Added list validation
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

            // Get checksheet item
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

            // SELALU CREATE DATA BARU (TIDAK UPDATE)
            $logDetail = LogDetailCs::create([
                'id_log' => $logCs->id_log,
                'station' => $validated['station'],
                'check_item' => $checksheetItem->check_item,
                'standard' => $checksheetItem->standard,
                'scanResult' => $scanResult,
                'list' => $validated['list'] ?? $checksheetItem->list, // Include list field
                'prod_status' => $validated['production_status'],
                'prod_checked_by' => Auth::check() ? Auth::user()->name : 'Produksi',
                'prod_checked_at' => now(),
                'quality_status' => null,
                'quality_checked_by' => null,
                'quality_checked_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('New detail created:', ['id' => $logDetail->id_det]);

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
                    'is_new_record' => true
                ]
            ];

            Log::info('Success response:', $response);
            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Validation error:', $e->errors());
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors()))
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
