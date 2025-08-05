<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $title }}
            </h2>
            
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('components.partials.toast')

                {{-- Informasi Umum --}}
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #3B82F6;">
                            <small class="text-gray-500 font-medium block">Area</small>
                            <div class="font-bold text-gray-800">{{ $log->area }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #22C55E;">
                            <small class="text-gray-500 font-medium block">Line</small>
                            <div class="font-bold text-gray-800">{{ $log->line }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #FACC15;">
                            <small class="text-gray-500 font-medium block">Model</small>
                            <div class="font-bold text-gray-800">{{ $log->model }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #06B6D4;">
                            <small class="text-gray-500 font-medium block">Shift & Date</small>
                            <div class="font-bold text-gray-800">{{ $log->shift }} - {{ Carbon\Carbon::parse($log->date)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>

                @if($isFullyValidated)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-green-800 font-medium">Semua item sudah divalidasi oleh Quality</span>
                        </div>
                    </div>
                @endif

                @php $grouped = $log->details->groupBy('station'); @endphp

                @foreach($grouped as $station => $items)
                <div class="mb-6 border rounded-lg shadow-sm">
                    <div class="flex justify-between items-center px-4 py-3 bg-gray-100 border-b">
                        <div class="font-semibold text-lg text-gray-700">Station: {{ $station }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $items->whereNotNull('quality_status')->count() }}/{{ $items->count() }} items validated
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-3 py-2 w-[50px]">List</th>
                                    <th class="border px-3 py-2 w-[200px]">Check Item</th>
                                    <th class="border px-3 py-2 w-[300px]">Standard</th>
                                    <th class="border px-3 py-2 w-[200px]">Scan Result</th>
                                    <th class="border px-3 py-2 w-[100px]">Prod Status</th>
                                    <th class="border px-3 py-2 w-[100px]">Prod Image</th> {{-- New column for Production Image --}}
                                    <th class="border px-3 py-2 w-[150px]">Quality Validation</th>
                                    <th class="border px-3 py-2 w-[100px]">Quality Status</th>
                                    <th class="border px-3 py-2 w-[100px]">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                @php
                                $extension = strtolower(pathinfo($item->check_item, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['png', 'jpg', 'jpeg']);
                                @endphp
                                <tr id="row-{{ $item->id_det }}" class="{{ $item->quality_status ? 'bg-green-50' : '' }}">
                                    <td class="border px-3 py-2">{{ $item->list }}</td>
                                    <td class="border px-3 py-2">
                                        @if ($isImage)
                                        <img src="{{ asset('storage/' . $item->check_item) }}"
                                            alt="Check Item Image" class="w-30 h-auto rounded shadow cursor-pointer" onclick="openImageModal(this.src)">
                                        @else
                                        {{ $item->check_item }}
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">{{ $item->standard }}</td>
                                    <td class="border px-3 py-2">
                                        @if($item->scanResult)
                                            @php
                                            $scanExtension = strtolower(pathinfo($item->scanResult, PATHINFO_EXTENSION));
                                            $isScanImage = in_array($scanExtension, ['png', 'jpg', 'jpeg']);
                                            @endphp
                                            @if($isScanImage)
                                                <img src="{{ $item->scanResult }}" alt="Scan Result" class="w-20 h-20 object-contain border rounded cursor-pointer" onclick="openImageModal(this.src)">
                                            @else
                                                {{ $item->scanResult }}
                                            @endif
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span class="font-semibold {{ $item->prod_status === 'OK' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $item->prod_status }}
                                        </span>
                                        
                                    </td>
                                    {{-- New column for Production Image --}}
                                    <td class="border px-3 py-2 text-center">
                                        @if($item->resultImage)
                                            <img src="{{ $item->resultImage }}" alt="Production Image" class="w-20 h-20 object-contain border rounded mx-auto cursor-pointer" onclick="openImageModal(this.src)">
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">
                                        @if($item->quality_status)
                                            <span class="font-semibold {{ $item->quality_status === 'OK' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $item->quality_status }}
                                            </span>
                                            
                                        @else
                                            <select id="quality-select-{{ $item->id_det }}"
                                                class="border rounded px-2 py-1 text-sm w-full"
                                                onchange="updateQualityStatus('{{ $item->id_det }}')">
                                                <option value="">Pilih Status</option>
                                                <option value="OK">OK</option>
                                                <option value="NG">NG</option>
                                            </select>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span id="quality-status-{{ $item->id_det }}"
                                            class="font-semibold {{ $item->quality_status === 'OK' ? 'text-green-600' : ($item->quality_status === 'NG' ? 'text-red-600' : 'text-gray-700') }}">
                                            {{ $item->quality_status ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="border px-3 py-2 text-center">
                                        @if($item->quality_status)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Validated
                                            </span>
                                        @else
                                            <button type="button" onclick="saveQualityValidation('{{ $item->id_det }}')"
                                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                id="validate-button-{{ $item->id_det }}" disabled>
                                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Validate
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Image Detail Modal --}}
    <div id="imageDetailModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center hidden z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-xl w-full mx-auto p-4 relative">
            <button onclick="closeImageModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl font-bold">&times;</button>
            <img id="modalImage" src="/placeholder.svg" alt="Detail Image" class="max-w-full max-h-[80vh] mx-auto object-contain rounded-md">
        </div>
    </div>

    <script>
    let qualityValues = {}; // Store selected quality values for each item
    let validatedItems = new Set(); // Track validated items

    function updateQualityStatus(itemId) {
        const select = document.getElementById(`quality-select-${itemId}`);
        const status = document.getElementById(`quality-status-${itemId}`);
        const validateButton = document.getElementById(`validate-button-${itemId}`);

        if (select.value) {
            qualityValues[itemId] = select.value;
            status.innerText = select.value;
            status.className = `font-semibold ${select.value === 'OK' ? 'text-green-600' : 'text-red-600'}`;
            
            // Enable validate button
            validateButton.disabled = false;
            validateButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            status.innerText = '-';
            status.className = 'font-semibold text-gray-700';
            validateButton.disabled = true;
            validateButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function saveQualityValidation(itemId) {
        // Check if item already validated
        if (validatedItems.has(itemId)) {
            showToast('error', 'Item ini sudah divalidasi sebelumnya.');
            return;
        }

        const validateButton = document.getElementById(`validate-button-${itemId}`);
        const select = document.getElementById(`quality-select-${itemId}`);
        const row = document.getElementById(`row-${itemId}`);
        
        if (!qualityValues[itemId]) {
            showToast('error', 'Silakan pilih status validasi terlebih dahulu.');
            return;
        }

        // Disable button and show loading
        validateButton.disabled = true;
        validateButton.innerHTML = `
            <svg class="animate-spin w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Validating...
        `;

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('detail_id', itemId);
        formData.append('quality_status', qualityValues[itemId]);

        fetch("{{ route('quality.validation.save') }}", {
                method: "POST",
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mark item as validated
                    validatedItems.add(itemId);
                    
                    // Update button to permanent "Validated" state
                    validateButton.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Validated
                    `;
                    validateButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    validateButton.classList.add('bg-green-600', 'cursor-not-allowed');
                    validateButton.disabled = true;
                    
                    // Add success styling to the row
                    row.classList.add('bg-green-50');
                    
                    // Disable select
                    select.disabled = true;
                    select.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                    
                    // Update quality validation column with validated info
                    const qualityValidationCell = select.closest('td');
                    qualityValidationCell.innerHTML = `
                        <span class="font-semibold ${data.data.quality_status === 'OK' ? 'text-green-600' : 'text-red-600'}">
                            ${data.data.quality_status}
                        </span>
                        
                    `;
                    
                    showToast('success', 'Validasi berhasil disimpan!');
                } else {
                    showToast('error', data.message || 'Gagal menyimpan validasi.');
                    validateButton.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Validate
                    `;
                    validateButton.disabled = false;
                    validateButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            })
            .catch(err => {
                console.error('Validation error:', err);
                showToast('error', 'Terjadi kesalahan saat menyimpan validasi.');
                validateButton.innerHTML = `
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Validate
                `;
                validateButton.disabled = false;
                validateButton.classList.remove('opacity-50', 'cursor-not-allowed');
            });
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.innerText = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // --- Image Detail Modal Functions ---
    const imageDetailModal = document.getElementById('imageDetailModal');
    const modalImage = document.getElementById('modalImage');

    function openImageModal(imageUrl) {
        modalImage.src = imageUrl;
        imageDetailModal.classList.remove('hidden');
    }

    function closeImageModal() {
        imageDetailModal.classList.add('hidden');
        modalImage.src = ''; // Clear image source
    }
    </script>
</x-app-layout>
