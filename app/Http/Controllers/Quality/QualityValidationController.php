<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogCs;
use App\Models\LogDetailCs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\PartModel;

class QualityValidationController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Build query for LogCs
        $query = LogCs::with(['details' => function($q) {
            $q->whereNotNull('prod_status'); 
        }, 'partModelRelation']); 

        // Apply filters
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        } else {
            // Default to today if no date specified
            $query->where('date', $today);
        }

        if ($request->filled('area')) {
            $query->where('area', $request->area);
        }

        if ($request->filled('line')) {
            $query->where('line', $request->line);
        }

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        $perPage = $request->get('per_page', 10);
        $logData = $query->orderBy('date', 'desc')
                        ->paginate($perPage)
                        ->appends($request->query());
        // Add validation status to each log
        foreach ($logData as $log) {
            $totalDetails = $log->details->count();
            $validatedDetails = $log->details->whereNotNull('quality_status')->count();
            
            $log->total_items = $totalDetails;
            $log->validated_items = $validatedDetails;
            $log->is_fully_validated = $totalDetails > 0 && $totalDetails === $validatedDetails;
            $log->validation_percentage = $totalDetails > 0 ? round(($validatedDetails / $totalDetails) * 100, 1) : 0;
        }

        $areas = LogCs::select('area')->distinct()->orderBy('area')->pluck('area');
        $lines = LogCs::select('line')->distinct()->orderBy('line')->pluck('line');
        
        // Get models with frontView mapping for dropdown
        $modelData = PartModel::select('Model', 'frontView')
            ->whereNotNull('frontView')
            ->where('frontView', '!=', '')
            ->distinct()
            ->orderBy('frontView')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->Model => $item->frontView];
            });

        return view('quality.validation.index', [
            'title' => 'Validation Checksheet',
            'logData' => $logData,
            'areas' => $areas,
            'lines' => $lines,
            'models' => $modelData, 
            'today' => $today,
            'tomorrow' => $tomorrow,
            'filters' => $request->all()
        ]);
    }

    public function validate($logId)
    {
        $log = LogCs::with(['details' => function($q) {
            $q->whereNotNull('prod_status')->orderBy('list', 'asc');
        }, 'partModelRelation'])->findOrFail($logId);

        // Check if all items are already validated
        $totalDetails = $log->details->count();
        $validatedDetails = $log->details->whereNotNull('quality_status')->count();
        $isFullyValidated = $totalDetails > 0 && $totalDetails === $validatedDetails;

        return view('quality.validation.form', [
            'title' => 'Validasi Quality',
            'log' => $log,
            'isFullyValidated' => $isFullyValidated
        ]);
    }

    public function saveValidation(Request $request)
    {
        Log::info('Quality validation request:', $request->all());
        
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'detail_id' => 'required|integer|exists:log_detail_cs,id_det',
                'quality_status' => 'required|string|in:OK,NG'
            ]);

            // Find the detail record
            $logDetail = LogDetailCs::findOrFail($validated['detail_id']);

            // Check if already validated
            if ($logDetail->quality_status !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item ini sudah divalidasi sebelumnya.'
                ], 400);
            }

            // Update quality validation
            $logDetail->update([
                'quality_status' => $validated['quality_status'],
                'quality_checked_by' => Auth::check() ? Auth::user()->name : 'Quality',
                'quality_checked_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Validasi berhasil disimpan',
                'data' => [
                    'detail_id' => $logDetail->id_det,
                    'quality_status' => $logDetail->quality_status,
                    'quality_checked_by' => $logDetail->quality_checked_by,
                    'quality_checked_at' => $logDetail->quality_checked_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Quality validation error:', $e->errors());
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Quality validation error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan validasi: ' . $e->getMessage()
            ], 500);
        }
    }
}