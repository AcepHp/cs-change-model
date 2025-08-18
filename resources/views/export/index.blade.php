<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Filter Data Export</h3>
                    <p class="text-sm text-gray-600 mt-1">Pilih filter untuk data yang akan diekspor</p>
                </div>

                <div class="p-6">
                    <!-- Baris 1: Date & Shift -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Date Filter -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Shift Filter -->
                        <div>
                            <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">Shift</label>
                            <select name="shift" id="shift"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Shift --</option>
                                <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 2: Area, Line, Model -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Area Filter -->
                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area</label>
                            <select name="area" id="area"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>
                                    {{ $area }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Line Filter -->
                        <div>
                            <label for="line" class="block text-sm font-medium text-gray-700 mb-2">Line</label>
                            <select name="line" id="line"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Line --</option>
                                @foreach($lines as $line)
                                <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>
                                    {{ $line }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Model Filter -->
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                            <select name="model" id="model"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Model --</option>
                                @foreach($models as $modelKey => $frontView)
                                <option value="{{ $modelKey }}" {{ request('model') == $modelKey ? 'selected' : '' }}>
                                    {{ $frontView }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="flex gap-2">
                            <button type="button" onclick="clearFilters()"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg 
                                    font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 
                                    focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 
                                    focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581
                                        m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Filter
                            </button>
                        </div>

                        <div class="flex gap-2">
                            <!-- PDF export -->
                            <button type="button" onclick="exportPdf()" id="pdfBtn"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg 
                                    font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 
                                    focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 
                                    focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                                        a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export PDF
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal: No Data -->
    <div id="noDataModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Tidak Ada Data</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Tidak ada data untuk diekspor dengan filter yang dipilih. Silakan ubah filter dan coba lagi.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function clearFilters() {
        document.getElementById('date').value = '';
        document.getElementById('shift').value = '';
        document.getElementById('area').value = '';
        document.getElementById('line').value = '';
        document.getElementById('model').value = '';
    }

    function exportPdf() {
        const button = document.getElementById('pdfBtn');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = 'Exporting PDF...';

        const filters = {
            date: document.getElementById('date').value,
            shift: document.getElementById('shift').value,
            area: document.getElementById('area').value,
            line: document.getElementById('line').value,
            model: document.getElementById('model').value
        };

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        Object.keys(filters).forEach(key => {
            if (filters[key] && filters[key].trim() !== '') {
                formData.append(key, filters[key].trim());
            }
        });

        fetch('{{ route("export.pdf") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.blob();
            } else if (response.status === 404) {
                throw new Error("NO_DATA");
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'Export failed');
                });
            }
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'AVI_Checksheet_Report_' + new Date().toISOString().slice(0,19).replace(/:/g, '-') + '.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            if (error.message === "NO_DATA") {
                document.getElementById('noDataModal').classList.remove('hidden');
            } else {
                alert(error.message);
            }
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('noDataModal');
        const closeButton = document.getElementById('closeModal');

        closeButton.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
    </script>
</x-app-layout>
