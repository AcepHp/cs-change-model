<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChangeModel;
use App\Models\PartModel;

class ChangeModelController extends Controller
{
    public function index(Request $request){
        $query = ChangeModel::query();

        if($request->filled('area')){
            $query->where('area', $request->area);
        }
        if($request->filled('line')){
            $query->where('line', $request->line);
        }
        if($request->filled('model')){
            $query->where('model', $request->model);
        }

        $dataChecksheet = $query->orderBy('id', 'asc')->paginate(10);

        // Get all unique models from the current page data
        $models = $dataChecksheet->pluck('model')->unique()->filter();
        
        // Get frontView data for all models at once to avoid N+1 queries
        $frontViewData = PartModel::whereIn('Model', $models)
            ->pluck('frontView', 'Model')
            ->toArray();

        // Add frontView to each item
        $dataChecksheet->getCollection()->transform(function ($item) use ($frontViewData) {
            $item->frontView = $frontViewData[$item->model] ?? $item->model;
            return $item;
        });

        $areas = ChangeModel::select('area')->distinct()->pluck('area')->filter();
        $lines = ChangeModel::select('line')->distinct()->pluck('line')->filter();
        
        $modelOptions = PartModel::select('Model', 'frontView')->distinct()->get();

        // Add existing log details if needed (keeping the original logic)
        $existingLogDetails = [];

        return view('data-master.index', compact(
            'dataChecksheet', 'areas', 'lines', 'modelOptions', 'existingLogDetails'
        ));
    }

    public function show($id){
        $item = ChangeModel::findOrFail($id);

        // Ambil frontView seperti di index
        $frontViewData = PartModel::where('Model', $item->model)
            ->pluck('frontView', 'Model')
            ->toArray();

        $frontView = $frontViewData[$item->model] ?? $item->model;

        return view('data-master.show', compact('item', 'frontView'));
    }


    
    public function create(){
        $areas = ChangeModel::select('area')->distinct()->pluck('area');
        $lines = ChangeModel::select('line')
        ->distinct()
        ->pluck('line')
        ->filter()
        ->sortBy(function ($line) {
            if (is_numeric($line)) {
                return (int) $line;
            }
            return 10000 + ord(strtolower($line[0]));
        })
        ->values();

        $modelOptions = PartModel::select('Model', 'frontView')->distinct()->get();
        $stations = ChangeModel::select('station')->distinct()->pluck('station');

        return view('data-master.create', compact('areas', 'lines', 'modelOptions', 'stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'nullable|string|max:9',
            'line' => 'nullable|string|max:9',
            'model' => 'nullable|string|max:50',
            'list' => 'nullable|integer',
            'station' => 'nullable|string|max:255',
            'check_item' => 'nullable|string|max:500',
            'standard' => 'nullable|string|max:500',
            'actual' => 'nullable|string|max:255',
            'trigger' => 'nullable|string|max:255',
            'image_type' => 'nullable|string|in:labelImage,tagImage,pacoImage',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // Upload file ke storage/app/public/images
        if ($request->hasFile('image_file')) {
            $imageName = time() . '-' . $request->image_type . '.' . $request->file('image_file')->extension();

            // Simpan ke storage/app/public/images
            $request->file('image_file')->storeAs('images', $imageName, 'public');

            // Simpan path relatif ke database
            $data['check_item'] = 'images/' . $imageName;
        }

        ChangeModel::create($data);

        return redirect()->route('dataMaster.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = ChangeModel::findOrFail($id);

        $lines = ChangeModel::select('line')
            ->distinct()
            ->pluck('line')
            ->filter()
            ->sortBy(function ($line) {
                if (is_numeric($line)) return (int) $line;
                return 10000 + ord(strtolower($line[0]));
            })
            ->values();

        $areas = ChangeModel::select('area')->distinct()->pluck('area');
        $modelOptions = PartModel::select('Model', 'frontView')->distinct()->get();
        $stations = ChangeModel::select('station')->distinct()->pluck('station');

        return view('data-master.edit', compact('item', 'areas', 'lines', 'modelOptions', 'stations'));
    }

    public function update(Request $request, $id)
    {
        $item = ChangeModel::findOrFail($id);

        $request->validate([
            'area' => 'nullable|string|max:9',
            'line' => 'nullable|string|max:9',
            'model' => 'nullable|string|max:50',
            'list' => 'nullable|integer',
            'station' => 'nullable|string|max:255',
            'check_item' => 'nullable|string|max:500',
            'standard' => 'nullable|string|max:500',
            'actual' => 'nullable|string|max:255',
            'trigger' => 'nullable|string|max:255',
            'image_type' => 'nullable|string|in:labelImage,tagImage,pacoImage',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        // Upload file baru jika ada
        if ($request->hasFile('image_file')) {
            $imageName = time() . '-' . $request->image_type . '.' . $request->file('image_file')->extension();
            $request->file('image_file')->storeAs('images', $imageName, 'public');

            // Hapus file lama jika ada
            if ($item->check_item && \Storage::disk('public')->exists($item->check_item)) {
                \Storage::disk('public')->delete($item->check_item);
            }

            $data['check_item'] = 'images/' . $imageName;
        }

        $item->update($data);

        return redirect()->route('dataMaster.index')->with('success', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        ChangeModel::findOrFail($id)->delete();
        return redirect()->route('dataMaster.index')->with('success', 'Data berhasil dihapus.');
    }
}
