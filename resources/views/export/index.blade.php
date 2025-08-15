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
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
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

                <form method="GET" action="{{ route('export.index') }}" id="filterForm" class="p-6">
                    <!-- Bagian atas: 2 kolom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Tanggal -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="date" id="date" value="{{ request('date') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>

                        <!-- Shift -->
                        <div>
                            <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">Shift <span
                                    class="text-red-500">*</span></label>
                            <select name="shift" id="shift"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bagian bawah: 3 kolom -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Area -->
                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area <span
                                    class="text-red-500">*</span></label>
                            <select name="area" id="area"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Area --</option>
                                @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Line -->
                        <div>
                            <label for="line" class="block text-sm font-medium text-gray-700 mb-2">Line <span
                                    class="text-red-500">*</span></label>
                            <select name="line" id="line"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Line --</option>
                                @foreach($lines as $line)
                                <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>{{ $line }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Model -->
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model <span
                                    class="text-red-500">*</span></label>
                            <select name="model" id="model"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                                <option value="">-- Pilih Model --</option>
                                @foreach($models as $model)
                                <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                    {{ $model }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="flex gap-2">
                            <button type="submit" id="previewBtn"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Preview Data
                            </button>
                            <button type="button" onclick="clearFilters()"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Reset Filter
                            </button>
                        </div>

                        @if($previewData && $totalRecords > 0)
                        <div class="flex gap-2">
                            <!-- Excel Export Form -->
                            <form method="POST" action="{{ route('export.excel') }}" id="excelForm" class="inline">
                                @csrf
                                <input type="hidden" name="date" value="{{ request('date') }}">
                                <input type="hidden" name="shift" value="{{ request('shift') }}">
                                <input type="hidden" name="area" value="{{ request('area') }}">
                                <input type="hidden" name="line" value="{{ request('line') }}">
                                <input type="hidden" name="model" value="{{ request('model') }}">

                            </form>

                            <!-- PDF Export Form -->
                            <form method="POST" action="{{ route('export.pdf') }}" id="pdfForm" class="inline">
                                @csrf
                                <input type="hidden" name="date" value="{{ request('date') }}">
                                <input type="hidden" name="shift" value="{{ request('shift') }}">
                                <input type="hidden" name="area" value="{{ request('area') }}">
                                <input type="hidden" name="line" value="{{ request('line') }}">
                                <input type="hidden" name="model" value="{{ request('model') }}">

                                <button type="button" onclick="validateAndSubmitExport('pdf')" id="pdfBtn"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export PDF
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </form>

            </div>

            <!-- Preview Section -->
            @if($previewData && $totalRecords > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Preview Data</h3>
                        <div class="flex items-center space-x-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Total: {{ number_format($totalRecords) }} records
                            </span>
                            <span class="text-sm text-gray-600">Menampilkan 10 data pertama</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Shift</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Area</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Line</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Model</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Station</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check Item</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prod Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quality Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($previewData as $index => $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($item->log->date ?? now())->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Shift {{ $item->log->shift ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->log->area ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->log->line ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->log->model ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->station }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">{{ $item->check_item }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($item->prod_status === 'OK')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                                    @elseif($item->prod_status === 'NG')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">NG</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($item->quality_status === 'OK')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>
                                    @elseif($item->quality_status === 'NG')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">NG</span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @elseif(request()->hasAny(['area', 'line', 'model', 'date', 'shift']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada data yang sesuai dengan filter yang dipilih.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Validation Modal -->
    <div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Filter Tidak Lengkap</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Semua field filter wajib diisi sebelum melakukan export data:
                    </p>
                    <ul id="missingFields" class="text-sm text-red-600 mt-2 text-left list-disc list-inside">
                        <!-- Missing fields will be populated here -->
                    </ul>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function clearFilters() {
        window.location.href = "{{ route('export.index') }}";
    }

    // Validasi semua field wajib diisi sebelum submit preview
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const date = document.getElementById('date').value;
        const shift = document.getElementById('shift').value;
        const area = document.getElementById('area').value;
        const line = document.getElementById('line').value;
        const model = document.getElementById('model').value;

        if (!date || !shift || !area || !line || !model) {
            e.preventDefault();
            showValidationModal('preview');
            return false;
        }
    });

    // Validasi untuk export
    function validateAndSubmitExport(type) {
        const date = document.getElementById('date').value;
        const shift = document.getElementById('shift').value;
        const area = document.getElementById('area').value;
        const line = document.getElementById('line').value;
        const model = document.getElementById('model').value;

        if (!date || !shift || !area || !line || !model) {
            showValidationModal('export');
            return false;
        }

        // Jika validasi berhasil, submit form yang sesuai
        if (type === 'excel') {
            document.getElementById('excelForm').submit();
        } else if (type === 'pdf') {
            document.getElementById('pdfForm').submit();
        }
    }

    function showValidationModal(action) {
        const date = document.getElementById('date').value;
        const shift = document.getElementById('shift').value;
        const area = document.getElementById('area').value;
        const line = document.getElementById('line').value;
        const model = document.getElementById('model').value;

        const missingFields = [];
        if (!date) missingFields.push('Tanggal');
        if (!shift) missingFields.push('Shift');
        if (!area) missingFields.push('Area');
        if (!line) missingFields.push('Line');
        if (!model) missingFields.push('Model');

        const missingFieldsList = document.getElementById('missingFields');
        missingFieldsList.innerHTML = '';

        missingFields.forEach(field => {
            const li = document.createElement('li');
            li.textContent = field;
            missingFieldsList.appendChild(li);
        });

        document.getElementById('validationModal').classList.remove('hidden');
    }

    // Close modal
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('validationModal').classList.add('hidden');
    });

    // Close modal when clicking outside
    document.getElementById('validationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Enhanced loading state for export buttons
    document.addEventListener('DOMContentLoaded', function() {
        const excelForm = document.getElementById('excelForm');
        const pdfForm = document.getElementById('pdfForm');

        if (excelForm) {
            excelForm.addEventListener('submit', function(e) {
                const button = document.getElementById('excelBtn');
                if (button) {
                    const originalText = button.innerHTML;

                    button.disabled = true;
                    button.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Exporting Excel...
                    `;

                    // Reset button after 10 seconds (fallback)
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }, 10000);
                }
            });
        }

        if (pdfForm) {
            pdfForm.addEventListener('submit', function(e) {
                const button = document.getElementById('pdfBtn');
                if (button) {
                    const originalText = button.innerHTML;

                    button.disabled = true;
                    button.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Exporting PDF...
                    `;

                    // Reset button after 10 seconds (fallback)
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }, 10000);
                }
            });
        }
    });
    </script>

</x-app-layout>