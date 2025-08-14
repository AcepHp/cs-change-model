<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Data Master') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Informasi Data Master</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Area --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Area:</span>
                            <span class="text-gray-800">{{ $item->area ?? '-' }}</span>
                        </div>

                        {{-- Line --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Line:</span>
                            <span class="text-gray-800">{{ $item->line ?? '-' }}</span>
                        </div>

                        {{-- Model --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Model:</span>
                            <span class="text-gray-800">{{ $item->model ?? '-' }}</span>
                        </div>

                        {{-- List --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">List:</span>
                            <span class="text-gray-800">{{ $item->list ?? '-' }}</span>
                        </div>

                        {{-- Station --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Station:</span>
                            <span class="text-gray-800">{{ $item->station ?? '-' }}</span>
                        </div>

                        {{-- Actual --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Actual:</span>
                            <span class="text-gray-800">{{ $item->actual ?? '-' }}</span>
                        </div>

                        {{-- Trigger --}}
                        <div class="p-3 bg-gray-50 rounded border">
                            <span class="font-medium text-gray-600">Trigger:</span>
                            <span class="text-gray-800">{{ $item->trigger ?? '-' }}</span>
                        </div>

                        {{-- Standard --}}
                        <div class="p-3 bg-gray-50 rounded border md:col-span-2">
                            <span class="font-medium text-gray-600">Standard:</span>
                            <span class="text-gray-800">{{ $item->standard ?? '-' }}</span>
                        </div>

                        {{-- Check Item --}}
                        <div class="p-3 bg-gray-50 rounded border md:col-span-2">
                            <span class="font-medium text-gray-600">Check Item:</span>
                            <span class="text-gray-800">{{ $item->check_item ?? '-' }}</span>
                        </div>

                        {{-- Image Preview (hanya jika check_item adalah file image) --}}
                        @if($item->check_item && Str::endsWith($item->check_item, ['.jpg', '.jpeg', '.png']))
                            <div class="p-3 bg-gray-50 rounded border md:col-span-2">
                                <span class="font-medium text-gray-600">Image:</span>
                                <div class="mt-2 flex justify-center">
                                    <img src="{{ asset('storage/' . $item->check_item) }}" alt="Image" class="w-64 rounded shadow-md border">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Kembali --}}
                    <div class="mt-6 text-right">
                        <a href="{{ route('dataMaster.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
