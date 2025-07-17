<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChangeModel;
use Carbon\Carbon;
use App\Models\LogCs;
use App\Models\LogDetailCs;


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

    
}
