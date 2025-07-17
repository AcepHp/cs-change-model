<x-app-layout>
    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>

    @include('components.partials.toast')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Management Data Master') }}
            </h2>
            <a href="{{ route('dataMaster.create') }}">
                <x-primary-button>Tambah Data</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div x-data="{ openModal: false, deleteUrl: '' }">
        <div x-show="openModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>

        <div class="py-10 relative z-0">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow rounded-lg relative z-10">
                    <div class="p-6 text-gray-900"
                        x-effect="document.body.style.overflow = openModal ? 'hidden' : 'auto'">

                        {{-- Filter --}}
                        <form method="GET" action="{{ route('dataMaster.index') }}"
                            class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-3 items-center">

                            <select name="area" class="border-gray-300 rounded-md">
                                <option value="">-- Semua Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area }}" @selected(request('area')==$area)>{{ $area }}</option>
                                @endforeach
                            </select>

                            <select name="line" class="border-gray-300 rounded-md">
                                <option value="">-- Semua Line --</option>
                                @foreach($lines as $line)
                                <option value="{{ $line }}" @selected(request('line')==$line)>{{ $line }}</option>
                                @endforeach
                            </select>

                            <select name="model" class="border-gray-300 rounded-md">
                                <option value="">-- Semua Model --</option>
                                @foreach($models as $model)
                                <option value="{{ $model }}" @selected(request('model')==$model)>{{ $model }}</option>
                                @endforeach
                            </select>

                            <div class="flex gap-2">
                                {{-- Tombol Filter --}}
                                <x-primary-button type="submit">Filter</x-primary-button>

                                {{-- Tombol Reset --}}
                                <x-secondary-button type="button"
                                    onclick="window.location.href='{{ route('dataMaster.index') }}'">
                                    Reset
                                </x-secondary-button>
                            </div>
                        </form>


                        {{-- Table --}}
                        <div class="overflow-x-auto border border-gray-200 rounded">
                            <div class="max-h-[400px] overflow-y-auto">
                                <table class="w-full text-sm text-left text-gray-700">
                                    <thead class="bg-gray-100 text-gray-800 text-sm uppercase sticky top-0 z-10">
                                        <tr>
                                            <th class="px-4 py-3 text-center w-5">No</th>
                                            <th class="px-4 py-3">Area</th>
                                            <th class="px-4 py-3">Line</th>
                                            <th class="px-4 py-3">Model</th>
                                            <th class="px-4 py-3">List</th>
                                            <th class="px-4 py-3">Station</th>
                                            <th class="px-4 py-3">Check Item</th>
                                            <th class="px-4 py-3">Standard</th>
                                            <th class="px-4 py-3">Actual</th>
                                            <th class="px-4 py-3">Trigger</th>
                                            <th class="px-4 py-3 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dataChecksheet as $i => $item)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-3 text-center w-5">{{ $dataChecksheet->firstItem() + $i }}</td>
                                            <td class="p-3">{{ $item->area }}</td>
                                            <td class="p-3">{{ $item->line }}</td>
                                            <td class="p-3">{{ $item->model }}</td>
                                            <td class="p-3">{{ $item->list }}</td>
                                            <td class="p-3">{{ $item->station }}</td>
                                            <td class="p-3">{{ $item->check_item }}</td>
                                            <td class="p-3">{{ $item->standard }}</td>
                                            <td class="p-3">{{ $item->actual }}</td>
                                            <td class="p-3">{{ $item->trigger }}</td>
                                            <td class="p-3">
                                                <div class="flex justify-center">
                                                    <a href="{{ route('dataMaster.show', $item->id) }}">
                                                        <x-primary-button
                                                            class="bg-blue-400 hover:bg-blue-500 text-white px-3 py-1 me-2 text-sm">
                                                            Lihat
                                                        </x-primary-button>
                                                    </a>
                                                    <a href="{{ route('dataMaster.edit', $item->id) }}">
                                                        <x-primary-button
                                                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 me-2 text-sm">
                                                            Edit
                                                        </x-primary-button>
                                                    </a>
                                                    <x-primary-button
                                                        @click="openModal = true; deleteUrl = '{{ route('dataMaster.destroy', $item->id) }}'"
                                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 text-sm">
                                                        Hapus
                                                    </x-primary-button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="p-3 border text-center text-gray-500">
                                                Belum ada data.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $dataChecksheet->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Hapus --}}
        <div x-show="openModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click.away="openModal = false" class="bg-white rounded-xl shadow-lg max-w-lg w-[480px] mx-4 p-6 z-50">
                <h2 class="text-xl font-bold mb-4">Konfirmasi Hapus</h2>
                <p class="text-sm text-gray-600 mb-6">
                    Yakin ingin menghapus data ini? Data tidak dapat dipulihkan.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="openModal = false"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>