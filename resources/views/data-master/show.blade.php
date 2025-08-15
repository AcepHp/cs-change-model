<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Data Master') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
                <div class="px-6 py-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 border-b pb-3">Informasi Data Master</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Area --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Area</p>
                            <p class="text-gray-900 font-semibold">{{ $item->area ?? '-' }}</p>
                        </div>

                        {{-- Line --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Line</p>
                            <p class="text-gray-900 font-semibold">{{ $item->line ?? '-' }}</p>
                        </div>

                        {{-- Model --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Model</p>
                            <p class="text-gray-900 font-semibold">{{ $item->model ?? '-' }}</p>
                        </div>

                        {{-- List --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">List</p>
                            <p class="text-gray-900 font-semibold">{{ $item->list ?? '-' }}</p>
                        </div>

                        {{-- Station --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Station</p>
                            <p class="text-gray-900 font-semibold">{{ $item->station ?? '-' }}</p>
                        </div>

                        {{-- Actual --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Actual</p>
                            <p class="text-gray-900 font-semibold">{{ $item->actual ?? '-' }}</p>
                        </div>

                        {{-- Trigger --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition">
                            <p class="text-sm font-medium text-gray-500">Actual Value</p>
                            <p class="text-gray-900 font-semibold">{{ $item->trigger ?? '-' }}</p>
                        </div>

                        {{-- Standard --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Standard</p>
                            <p class="text-gray-900 font-semibold">{{ $item->standard ?? '-' }}</p>
                        </div>

                        {{-- Check Item --}}
                        <div class="p-4 bg-gray-50 rounded-lg border hover:shadow-md transition md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Check Item</p>
                            <p class="text-gray-900 font-semibold">{{ $item->check_item ?? '-' }}</p>
                        </div>

                        {{-- Image Preview --}}
                        @if($item->check_item && Str::endsWith($item->check_item, ['.jpg', '.jpeg', '.png']))
                        <div class="p-4 bg-gray-50 rounded-lg border md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Image</p>
                            <div class="mt-3 flex justify-center">
                                <img src="{{ asset('storage/' . $item->check_item) }}" alt="Image"
                                    class="max-w-full md:max-w-sm rounded-lg shadow-md border">
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Tombol Kembali --}}
                    <div class="mt-8 text-center md:text-right">
                        <a href="{{ route('dataMaster.index') }}"
                            class="px-5 py-2 bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>