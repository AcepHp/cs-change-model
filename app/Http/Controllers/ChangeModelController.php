<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChangeModel;

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

        $dataChecksheet = $query->orderBy('id', 'desc')->paginate(10);

        $areas = ChangeModel::select('area')->distinct()->pluck('area')->filter();
        $lines = ChangeModel::select('line')->distinct()->pluck('line')->filter();
        $models = ChangeModel::select('model')->distinct()->pluck('model')->filter();

        return view('data-master.index', compact(
            'dataChecksheet', 'areas', 'lines', 'models'
        ));
    }

    public function show($id){
        $item = ChangeModel::findOrFail($id);
        return view('data-master.show', compact('item'));
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

        $models = ChangeModel::select('model')->distinct()->pluck('model');
        $stations = ChangeModel::select('station')->distinct()->pluck('station');

        return view('data-master.create', compact('areas', 'lines', 'models', 'stations'));
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
        $models = ChangeModel::select('model')->distinct()->pluck('model');
        $stations = ChangeModel::select('station')->distinct()->pluck('station');

        return view('data-master.edit', compact('item', 'areas', 'lines', 'models', 'stations'));
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
