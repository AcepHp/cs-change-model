<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert untuk menampilkan pesan error --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Enhanced alert container for AJAX responses with info and warning styles --}}
            <div id="ajax-alert" class="hidden mb-6">
                <div id="ajax-alert-content" class="rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg id="ajax-alert-icon" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 id="ajax-alert-title" class="text-sm font-medium">Info</h3>
                            <div class="mt-2 text-sm">
                                <p id="ajax-alert-message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced filter form with better styling and responsive design --}}
            <div class="bg-white shadow-lg sm:rounded-lg border border-gray-200">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 sm:rounded-t-lg">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 2v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filter Data Checksheet
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Pilih kriteria untuk menampilkan preview dan export PDF</p>
                </div>
                
                <div class="p-6">
                    <form id="filter-form">
                        @csrf
                        {{-- Baris atas: Shift & Tanggal --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="shift" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Shift <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="shift" name="shift"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition duration-200">
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1 (07:00 - 19:00)</option>
                                    <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2 (19:00 - 07:00)</option>
                                </select>
                            </div>

                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Tanggal <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="date" id="date" name="date"
                                    value="{{ request('date', $today) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition duration-200">
                            </div>
                        </div>

                        {{-- Baris bawah: Area, Line, Model --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Area <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="area" name="area"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition duration-200">
                                    <option value="">-- Pilih Area --</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="line" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Line <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="line" name="line"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition duration-200">
                                    <option value="">-- Pilih Line --</option>
                                    @foreach($lines as $line)
                                    <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>{{ $line }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Model <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select id="model" name="model"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition duration-200">
                                    <option value="">-- Pilih Model --</option>
                                    @foreach($models as $model)
                                    <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Enhanced action buttons with better styling --}}
                        <div class="mt-8 flex flex-col sm:flex-row justify-between gap-4">
                            <button type="button" id="reset-btn" 
                                class="inline-flex items-center px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Filter
                            </button>
                            
                            <button type="button" id="preview-btn"
                                class="inline-flex items-center px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="preview-btn-text" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Preview Data
                                </span>
                                <span id="preview-btn-loading" class="hidden flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memuat...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Added preview container that shows below the filter -->
            <div id="preview-container" class="hidden mt-6">
                <!-- Filter Summary -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Filter yang Dipilih
                    </h3>
                    <div id="filter-summary" class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                    <button type="button" id="hide-preview-btn"
                           class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                        </svg>
                        Sembunyikan Preview
                    </button>
                    
                    <form id="pdf-form" method="GET" action="{{ route('export.generate') }}" class="inline">
                        <input type="hidden" id="pdf-shift" name="shift">
                        <input type="hidden" id="pdf-date" name="date">
                        <input type="hidden" id="pdf-area" name="area">
                        <input type="hidden" id="pdf-line" name="line">
                        <input type="hidden" id="pdf-model" name="model">
                        
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </button>
                    </form>
                </div>

                <!-- Preview Table Container -->
                <div id="preview-table-container">
                    <!-- Table will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Peringatan -->
    <div id="warningModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md text-center">
            <h2 class="text-lg font-semibold mb-4 text-red-600">Peringatan</h2>
            <p class="text-gray-700 mb-6">Silakan isi semua filter sebelum menekan tombol preview.</p>
            <x-primary-button onclick="closeModal()" class="text-white px-4 py-2 rounded hover:bg-black">
                Tutup
            </x-primary-button>
        </div>
    </div>

    <!-- Enhanced JavaScript with better error handling and UI feedback -->
    <script>
        document.getElementById('preview-btn').addEventListener('click', function(event) {
            event.preventDefault();

            const requiredFields = ['shift', 'date', 'area', 'line', 'model'];
            let valid = true;

            requiredFields.forEach(function(id) {
                const field = document.getElementById(id);
                if (!field.value) {
                    valid = false;
                }
            });

            if (!valid) {
                document.getElementById('warningModal').classList.remove('hidden');
                return;
            }

            // Show loading state
            document.getElementById('preview-btn-text').classList.add('hidden');
            document.getElementById('preview-btn-loading').classList.remove('hidden');
            document.getElementById('preview-btn').disabled = true;

            // Hide any previous alerts
            document.getElementById('ajax-alert').classList.add('hidden');

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('shift', document.getElementById('shift').value);
            formData.append('date', document.getElementById('date').value);
            formData.append('area', document.getElementById('area').value);
            formData.append('line', document.getElementById('line').value);
            formData.append('model', document.getElementById('model').value);

            // Make AJAX request
            fetch('{{ route("export.preview") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show preview container
                    document.getElementById('preview-container').classList.remove('hidden');
                    
                    // Update filter summary
                    const filterSummary = document.getElementById('filter-summary');
                    filterSummary.innerHTML = `
                        <div class="bg-white rounded p-3 border"><strong>Shift:</strong><br>${data.filters.shift}</div>
                        <div class="bg-white rounded p-3 border"><strong>Tanggal:</strong><br>${new Date(data.filters.date).toLocaleDateString('id-ID')}</div>
                        <div class="bg-white rounded p-3 border"><strong>Area:</strong><br>${data.filters.area}</div>
                        <div class="bg-white rounded p-3 border"><strong>Line:</strong><br>${data.filters.line}</div>
                        <div class="bg-white rounded p-3 border"><strong>Model:</strong><br>${data.filters.model}</div>
                    `;
                    
                    // Update preview table
                    document.getElementById('preview-table-container').innerHTML = data.html;
                    
                    // Update PDF form hidden inputs
                    document.getElementById('pdf-shift').value = data.filters.shift;
                    document.getElementById('pdf-date').value = data.filters.date;
                    document.getElementById('pdf-area').value = data.filters.area;
                    document.getElementById('pdf-line').value = data.filters.line;
                    document.getElementById('pdf-model').value = data.filters.model;
                    
                    if (data.message) {
                        showAlert('info', 'Informasi', data.message);
                    }
                    
                    // Scroll to preview
                    document.getElementById('preview-container').scrollIntoView({ behavior: 'smooth' });
                } else {
                    // Show error message
                    showAlert('error', 'Error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Terjadi kesalahan saat memuat data preview.');
            })
            .finally(() => {
                // Hide loading state
                document.getElementById('preview-btn-text').classList.remove('hidden');
                document.getElementById('preview-btn-loading').classList.add('hidden');
                document.getElementById('preview-btn').disabled = false;
            });
        });

        function showAlert(type, title, message) {
            const alertContainer = document.getElementById('ajax-alert');
            const alertContent = document.getElementById('ajax-alert-content');
            const alertIcon = document.getElementById('ajax-alert-icon');
            const alertTitle = document.getElementById('ajax-alert-title');
            const alertMessage = document.getElementById('ajax-alert-message');

            // Reset classes
            alertContent.className = 'rounded-lg p-4';
            alertIcon.className = 'h-5 w-5';
            alertTitle.className = 'text-sm font-medium';

            if (type === 'error') {
                alertContent.classList.add('bg-red-50', 'border', 'border-red-200');
                alertIcon.classList.add('text-red-400');
                alertTitle.classList.add('text-red-800');
                alertTitle.textContent = title;
            } else if (type === 'info') {
                alertContent.classList.add('bg-blue-50', 'border', 'border-blue-200');
                alertIcon.classList.add('text-blue-400');
                alertTitle.classList.add('text-blue-800');
                alertTitle.textContent = title;
                // Change icon for info
                alertIcon.innerHTML = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />';
            }

            alertMessage.textContent = message;
            alertContainer.classList.remove('hidden');
        }

        document.getElementById('hide-preview-btn').addEventListener('click', function() {
            document.getElementById('preview-container').classList.add('hidden');
        });

        document.getElementById('reset-btn').addEventListener('click', function() {
            document.getElementById('shift').value = '';
            document.getElementById('date').value = '{{ $today }}';
            document.getElementById('area').value = '';
            document.getElementById('line').value = '';
            document.getElementById('model').value = '';
            document.getElementById('preview-container').classList.add('hidden');
            document.getElementById('ajax-alert').classList.add('hidden');
        });

        function closeModal() {
            document.getElementById('warningModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
