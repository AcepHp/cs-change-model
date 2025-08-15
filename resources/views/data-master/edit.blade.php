<x-app-layout>
    @include('components.partials.toast')

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Master') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('dataMaster.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Area --}}
                            <div>
                                <label class="block font-medium text-gray-700">Area</label>
                                <select name="area" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    <option value="">-- Pilih Area --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}" {{ $item->area == $area ? 'selected' : '' }}>{{ $area }}</option>
                                    @endforeach
                                </select>
                                @error('area') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Line --}}
                            <div>
                                <label class="block font-medium text-gray-700">Line</label>
                                <select name="line" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    <option value="">-- Pilih Line --</option>
                                    @foreach ($lines as $line)
                                        <option value="{{ $line }}" {{ $item->line == $line ? 'selected' : '' }}>{{ $line }}</option>
                                    @endforeach
                                </select>
                                @error('line') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Model --}}
                            <div>
                                <label class="block font-medium text-gray-700">Model</label>
                                <select name="model" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    <option value="">-- Pilih Model --</option>
                                    @foreach ($models as $model)
                                        <option value="{{ $model }}" {{ $item->model == $model ? 'selected' : '' }}>{{ $model }}</option>
                                    @endforeach
                                </select>
                                @error('model') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- List --}}
                            <div>
                                <label class="block font-medium text-gray-700">List</label>
                                <input type="number" name="list" value="{{ old('list', $item->list) }}" class="form-input w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                @error('list') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Station --}}
                            <div>
                                <label class="block font-medium text-gray-700">Station</label>
                                <select name="station" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    <option value="">-- Pilih Station --</option>
                                    @foreach ($stations as $station)
                                        <option value="{{ $station }}" {{ $item->station == $station ? 'selected' : '' }}>{{ $station }}</option>
                                    @endforeach
                                </select>
                                @error('station') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Check Item --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-gray-700">Check Item</label>
                                <input type="text" name="check_item" value="{{ old('check_item', $item->check_item) }}" class="form-input w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                @error('check_item') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Standard --}}
                            <div class="md:col-span-2">
                                <label class="block font-medium text-gray-700">Standard</label>
                                <input type="text" name="standard" value="{{ old('standard', $item->standard) }}" class="form-input w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                @error('standard') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Actual + Trigger --}}
                            <div x-data="{ actual: '{{ old('actual', $item->actual) }}' }" class="md:col-span-2">
                                <label class="block text-sm text-gray-600 mb-1">
                                    <strong>Penjelasan:</strong><br>
                                    <ul class="list-disc ml-5 text-sm mt-1 space-y-1">
                                        <li><strong>CHECK:</strong> Saat pengisian checksheet, hanya tersedia pilihan OK
                                            atau NG (tidak ada proses scan).</li>
                                        <li><strong>SCAN:</strong> Sistem akan memverifikasi apakah hasil scan PERSIS
                                            SAMA dengan nilai ACTUAL VALUE.</li>
                                        <li><strong>CONTAINSCAN:</strong> Sistem akan memeriksa apakah hasil scan
                                            MENGANDUNG nilai ACTUAL VALUE di dalamnya.</li>
                                    </ul>
                                </label>

                                <label class="block font-medium text-gray-700">Actual</label>
                                <select name="actual" x-model="actual" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    <option value="">-- Pilih Actual --</option>
                                    <option value="check">Check</option>
                                    <option value="scan">Scan</option>
                                    <option value="containscan">ContainScan</option>
                                </select>
                                @error('actual') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

                                <div class="mt-4" x-show="actual === 'scan' || actual === 'containscan'">
                                    <label class="block font-medium text-gray-700">Actual Value</label>
                                    <input type="text" name="trigger" value="{{ old('trigger', $item->trigger) }}" class="form-input w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                    @error('trigger') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <template x-if="actual === 'check'">
                                    <input type="hidden" name="trigger" value="">
                                </template>
                            </div>

                            {{-- Tambahkan / Ganti Image --}}
                            <div x-data="{ addImage: {{ (Str::endsWith($item->check_item, ['.jpg', '.jpeg', '.png'])) ? 'true' : 'false' }} }" class="md:col-span-2">
                                <label class="block font-medium text-gray-700">
                                    <input type="checkbox" @change="addImage = !addImage" :checked="addImage"> Tambahkan / Ganti Image?
                                </label>

                                <div x-show="addImage" class="mt-3 space-y-3">
                                    {{-- Tipe Image --}}
                                    <label class="block font-medium text-gray-700">Tipe Image</label>
                                    <select name="image_type" class="form-select w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="labelImage" {{ $item->image_type == 'labelImage' ? 'selected' : '' }}>Label Image</option>
                                        <option value="tagImage" {{ $item->image_type == 'tagImage' ? 'selected' : '' }}>Tag Image</option>
                                        <option value="pacoImage" {{ $item->image_type == 'pacoImage' ? 'selected' : '' }}>Paco Image</option>
                                    </select>

                                    {{-- Preview image lama --}}
                                    @if(Str::endsWith($item->check_item, ['.jpg', '.jpeg', '.png']))
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">Gambar saat ini:</p>
                                            <img src="{{ asset('storage/' . $item->check_item) }}" alt="Image" class="w-48 border rounded">
                                        </div>
                                    @endif

                                    {{-- Upload File Baru --}}
                                    <label class="block font-medium text-gray-700">Upload Image Baru</label>
                                    <input type="file" name="image_file" accept="image/*" class="form-input w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-100">
                                </div>
                            </div>

                        </div>

                        {{-- Tombol --}}
                        <div class="flex justify-end gap-3 items-center pt-4">
                            <a href="{{ route('dataMaster.index') }}">
                                <u>Kembali</u>
                            </a>
                            <x-primary-button>UPDATE DATA</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
