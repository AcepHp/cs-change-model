<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
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
                            <div class="font-bold text-gray-800">{{ $area }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #22C55E;">
                            <small class="text-gray-500 font-medium block">Line</small>
                            <div class="font-bold text-gray-800">{{ $line }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #FACC15;">
                            <small class="text-gray-500 font-medium block">Model</small>
                            <div class="font-bold text-gray-800">{{ $model }}</div>
                        </div>
                        <div class="p-4 rounded-lg bg-gray-50 border-l-4" style="border-color: #06B6D4;">
                            <small class="text-gray-500 font-medium block">Shift & Date</small>
                            <div class="font-bold text-gray-800">{{ $shift }} - {{ $date }}</div>
                        </div>
                    </div>
                </div>

                @php $grouped = $results->groupBy('station'); @endphp

                @foreach($grouped as $station => $items)
                <div class="mb-6 border rounded-lg shadow-sm">
                    <div class="flex justify-between items-center px-4 py-3 bg-gray-100 border-b">
                        <div class="font-semibold text-lg text-gray-700">Station: {{ $station }}</div>
                        <button type="button" onclick="openStationScanner('{{ $station }}')"
                            class="inline-flex items-center px-4 py-1 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700 opacity-75"
                            id="station-scan-btn-{{ $station }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Scan Station (Locked)
                        </button>
                    </div>
                    <div class="overflow-x-auto" data-station="{{ $station }}">
                        <table class="table-auto w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="border px-3 py-2 w-[15px]">List</th>
                                    <th class="border px-3 py-2 w-[200px]">Check Item</th>
                                    <th class="border px-3 py-2 w-[300px]">Standard</th>
                                    <th class="border px-3 py-2">Trigger</th>
                                    <th class="border px-3 py-2 w-[150px]">Action</th>
                                    <th class="border px-3 py-2 w-[200px]">Value</th>
                                    <th class="border px-3 py-2 w-[100px]">Status</th>
                                    <th class="border px-3 py-2 w-[100px]">Submit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                @php
                                $extension = strtolower(pathinfo($item->check_item, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['png', 'jpg', 'jpeg']);
                                $actualType = strtolower($item->actual);
                                @endphp
                                <tr data-item-id="{{ $item->id }}" id="row-{{ $item->id }}">
                                    <td class="border px-3 py-2">{{ $item->list }}</td>
                                    <td class="border px-3 py-2">
                                        @if ($isImage)
                                        <img src="{{ asset('storage/' . $item->check_item) }}"
                                            alt="Check Item Image" class="w-30 h-auto rounded shadow">
                                        @else
                                        {{ $item->check_item }}
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">{{ $item->standard }}</td>
                                    <td class="border px-3 py-2" id="trigger-{{ $item->id }}">
                                        {{ $item->trigger }}</td>
                                    <td class="border px-3 py-2">
                                        @if($actualType === 'scan' || $actualType === 'containscan')
                                        <button type="button"
                                            onclick="openBarcodeScanner('{{ $item->id }}', '{{ $actualType }}')"
                                            class="px-3 py-1 bg-gray-400 text-white text-xs rounded opacity-50 cursor-not-allowed"
                                            id="scan-button-{{ $item->id }}" disabled>
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Scan Barcode
                                        </button>
                                        @elseif($actualType === 'check')
                                        <select id="check-select-{{ $item->id }}"
                                            class="border rounded px-2 py-1 text-sm w-full bg-gray-100 opacity-50 cursor-not-allowed"
                                            onchange="updateCheckStatus('{{ $item->id }}')" disabled>
                                            <option value="">Pilih</option>
                                            <option value="OK">OK</option>
                                            <option value="NG">NG</option>
                                        </select>
                                        @else
                                        <span class="text-gray-500 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span id="value-display-{{ $item->id }}"
                                            class="text-gray-600 italic">Belum diisi</span>
                                    </td>
                                    <td class="border px-3 py-2">
                                        <span id="status-{{ $item->id }}"
                                            class="font-semibold text-gray-700">-</span>
                                    </td>
                                    <td class="border px-3 py-2 text-center">
                                        <button type="button" onclick="saveItem('{{ $item->id }}')"
                                            class="px-3 py-1 bg-gray-400 text-white text-xs rounded opacity-50 cursor-not-allowed"
                                            id="submit-button-{{ $item->id }}" disabled>
                                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Simpan
                                        </button>
                                        <div id="ng-warning-{{ $item->id }}" class="text-red-500 text-xs mt-1 hidden">
                                            Status NG tidak dapat disubmit
                                        </div>
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

    {{-- Modal Scan Station --}}
    <div id="scannerStationModal"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-4 w-full max-w-md relative">
            <h2 class="text-lg font-semibold mb-4">Scan QR Station</h2>
            <div id="readerStation" class="w-full"></div>
            <button onclick="closeScanner('Station')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        </div>
    </div>

    {{-- Modal Scan Barcode --}}
    <div id="scannerBarcodeModal"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-4 w-full max-w-md relative">
            <h2 class="text-lg font-semibold mb-4">Scan QR Barcode</h2>
            <div id="readerBarcode" class="w-full"></div>
            <button onclick="closeScanner('Barcode')"
                class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl">&times;</button>
        </div>
    </div>

    {{-- Modal Alert Station --}}
    <div id="stationAlertModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md text-center">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Station Tidak Cocok</h3>
            <p id="stationAlertText" class="mb-4 text-gray-700"></p>
            <button onclick="closeModalAlert()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">OK</button>
        </div>
    </div>

    {{-- Include HTML5Qrcode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
    let qrScannerStation, qrScannerBarcode;
    let scannedStations = {};
    let itemValues = {}; // Store scanned/selected values for each item
    let submittedItems = new Set(); // Track submitted items

    function openStationScanner(station) {
        document.getElementById('scannerStationModal').classList.remove('hidden');
        if (!qrScannerStation) {
            qrScannerStation = new Html5Qrcode("readerStation");
        }

        qrScannerStation.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            qrCodeMessage => {
                if (qrCodeMessage.trim() === station.trim()) {
                    scannedStations[station] = true;

                    // Update station button to unlocked state
                    const stationBtn = document.getElementById(`station-scan-btn-${station}`);
                    stationBtn.className = "inline-flex items-center px-4 py-1 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700";
                    stationBtn.innerHTML = `
                        Station Unlocked
                    `;

                    // Enable all inputs for this station
                    document.querySelectorAll(`[data-station="${station}"] button, [data-station="${station}"] select`).forEach(el => {
                        if (!submittedItems.has(el.closest('tr').getAttribute('data-item-id'))) {
                            el.disabled = false;
                            el.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            
                            // Update button colors based on type
                            if (el.id && el.id.includes('scan-button')) {
                                el.classList.add('bg-blue-600', 'hover:bg-blue-700');
                                el.innerHTML = `
                                    Scan Barcode
                                `;
                            } else if (el.id && el.id.includes('submit-button')) {
                                el.classList.add('bg-green-600', 'hover:bg-green-700');
                                el.innerHTML = `
                                    Simpan
                                `;
                            } else if (el.tagName === 'SELECT') {
                                el.classList.remove('bg-gray-100');
                                el.classList.add('bg-white');
                            }
                        }
                    });

                    // Check if all stations are scanned
                    const allStations = [...new Set(Array.from(document.querySelectorAll('[data-station]')).map(
                        el => el.getAttribute('data-station')))];
                    const allScanned = allStations.every(st => scannedStations[st]);

                    if (allScanned) {
                        const submitBtn = document.getElementById('submitAllBtn');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                            submitBtn.innerHTML = `
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Semua (Hanya OK)
                            `;
                        }
                    }

                    showToast('success', `Station ${station} berhasil discan dan dibuka!`);
                } else {
                    showModalAlert(`Scan: ${qrCodeMessage}<br>Diharapkan: ${station}`);
                }

                closeScanner('Station');
            },
            error => {}
        ).catch(err => console.error("Camera error:", err));
    }

    function openBarcodeScanner(itemId, actualType) {
        // Check if item already submitted
        if (submittedItems.has(itemId)) {
            showToast('error', 'Item ini sudah disubmit dan tidak dapat diubah lagi.');
            return;
        }

        document.getElementById('scannerBarcodeModal').classList.remove('hidden');
        if (!qrScannerBarcode) {
            qrScannerBarcode = new Html5Qrcode("readerBarcode");
        }

        qrScannerBarcode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            qrCodeMessage => {
                const trigger = document.getElementById(`trigger-${itemId}`).innerText.trim();
                const display = document.getElementById(`value-display-${itemId}`);
                const status = document.getElementById(`status-${itemId}`);
                const submitButton = document.getElementById(`submit-button-${itemId}`);
                const ngWarning = document.getElementById(`ng-warning-${itemId}`);

                // Store the scanned value
                itemValues[itemId] = qrCodeMessage;

                // Update display
                const isImage = qrCodeMessage.match(/\.(jpeg|jpg|png)$/i);
                if (isImage) {
                    display.innerHTML =
                        `<img src="${qrCodeMessage}" alt="Scanned Image" class="w-32 h-32 object-contain border rounded">`;
                } else {
                    display.innerText = qrCodeMessage;
                }

                // Determine status based on actual type
                let statusValue = '';
                if (actualType === 'scan') {
                    statusValue = (qrCodeMessage === trigger) ? 'OK' : 'NG';
                } else if (actualType === 'containscan') {
                    statusValue = qrCodeMessage.includes(trigger) ? 'OK' : 'NG';
                }

                status.innerText = statusValue;
                status.className = `font-semibold ${statusValue === 'OK' ? 'text-green-600' : 'text-red-600'}`;

                // Enable/disable submit button based on status
                if (statusValue === 'NG') {
                    submitButton.disabled = true;
                    submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed', 'bg-red-400');
                    submitButton.innerHTML = `
                        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Tidak Bisa Submit
                    `;
                    ngWarning.classList.remove('hidden');
                } else {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-red-400');
                    submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
                    submitButton.innerHTML = `
                        Simpan
                    `;
                    ngWarning.classList.add('hidden');
                }

                closeScanner('Barcode');
            },
            error => {}
        ).catch(err => console.error("Camera error:", err));
    }

    function updateCheckStatus(itemId) {
        // Check if item already submitted
        if (submittedItems.has(itemId)) {
            showToast('error', 'Item ini sudah disubmit dan tidak dapat diubah lagi.');
            return;
        }

        const select = document.getElementById(`check-select-${itemId}`);
        const status = document.getElementById(`status-${itemId}`);
        const display = document.getElementById(`value-display-${itemId}`);
        const submitButton = document.getElementById(`submit-button-${itemId}`);
        const ngWarning = document.getElementById(`ng-warning-${itemId}`);

        if (select.value) {
            itemValues[itemId] = select.value;
            status.innerText = select.value;
            status.className = `font-semibold ${select.value === 'OK' ? 'text-green-600' : 'text-red-600'}`;
            display.innerText = select.value;

            // Enable/disable submit button based on status
            if (select.value === 'NG') {
                submitButton.disabled = true;
                submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                submitButton.classList.add('opacity-50', 'cursor-not-allowed', 'bg-red-400');
                submitButton.innerHTML = `
                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Tidak Bisa Submit
                `;
                ngWarning.classList.remove('hidden');
            } else {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-red-400');
                submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
                submitButton.innerHTML = `
                    Simpan
                `;
                ngWarning.classList.add('hidden');
            }
        } else {
            status.innerText = '-';
            status.className = 'font-semibold text-gray-700';
            display.innerText = 'Belum diisi';
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            ngWarning.classList.add('hidden');
        }
    }

    function saveItem(itemId) {
        // Check if item already submitted
        if (submittedItems.has(itemId)) {
            showToast('error', 'Item ini sudah disubmit dan tidak dapat diubah lagi.');
            return;
        }

        const submitButton = document.getElementById(`submit-button-${itemId}`);
        const statusSpan = document.getElementById(`status-${itemId}`);
        const row = document.getElementById(`row-${itemId}`);
        
        if (!itemValues[itemId] || !statusSpan.innerText || statusSpan.innerText === '-') {
            showToast('error', 'Data belum lengkap atau belum discan.');
            return;
        }

        // Validasi status NG
        if (statusSpan.innerText === 'NG') {
            showToast('error', 'Status NG tidak dapat disubmit. Silakan perbaiki terlebih dahulu.');
            return;
        }

        // Disable button and show loading
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin w-3 h-3 inline mr-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');

        // Prepare form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('item_id', itemId);
        formData.append('area', '{{ $area }}');
        formData.append('line', '{{ $line }}');
        formData.append('model', '{{ $model }}');
        formData.append('shift', '{{ $shift }}');
        formData.append('date', '{{ $date }}');
        formData.append('station', row.closest('[data-station]').getAttribute('data-station'));
        formData.append('scan_result', itemValues[itemId]);
        formData.append('production_status', statusSpan.innerText);
        formData.append('actual', itemValues[itemId]);

        fetch("{{ route('produksi.inputChecksheet.save') }}", {
                method: "POST",
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mark item as submitted
                    submittedItems.add(itemId);
                    
                    // Update button to permanent "Tersimpan" state
                    submitButton.innerHTML = `
                        Tersimpan
                    `;
                    submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                    submitButton.classList.add('bg-gray-500', 'cursor-not-allowed');
                    submitButton.disabled = true;
                    
                    // Add success styling to the row
                    row.classList.add('bg-green-50', 'border-green-200');
                    
                    // Disable all inputs for this item
                    const scanButton = document.getElementById(`scan-button-${itemId}`);
                    const selectElement = document.getElementById(`check-select-${itemId}`);
                    
                    if (scanButton) {
                        scanButton.disabled = true;
                        scanButton.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                        scanButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    }
                    
                    if (selectElement) {
                        selectElement.disabled = true;
                        selectElement.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-100');
                    }
                    
                    showToast('success', 'Data berhasil disimpan! Item ini tidak dapat diubah lagi.');
                } else {
                    showToast('error', data.message || 'Gagal menyimpan data.');
                    submitButton.innerHTML = `
                        Simpan
                    `;
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            })
            .catch(err => {
                console.error('Submit error:', err);
                showToast('error', 'Terjadi kesalahan saat menyimpan data.');
                submitButton.innerHTML = `
                    Simpan
                `;
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            });
    }

    function submitAll() {
        const allItems = document.querySelectorAll('[data-item-id]');
        let okItems = [];
        
        allItems.forEach(row => {
            const itemId = row.getAttribute('data-item-id');
            const submitButton = document.getElementById(`submit-button-${itemId}`);
            const status = document.getElementById(`status-${itemId}`);
            
            // Hanya submit item dengan status OK, tombol aktif, dan belum disubmit
            if (submitButton.innerText.includes('Simpan') && !submitButton.disabled && 
                status.innerText === 'OK' && !submittedItems.has(itemId)) {
                okItems.push(itemId);
            }
        });

        if (okItems.length === 0) {
            showToast('info', 'Tidak ada item dengan status OK yang siap disimpan.');
            return;
        }

        // Save all OK items
        okItems.forEach(itemId => {
            saveItem(itemId);
        });

        showToast('info', `Menyimpan ${okItems.length} item dengan status OK.`);
    }

    function closeScanner(type) {
        const modalId = type === 'Station' ? 'scannerStationModal' : 'scannerBarcodeModal';
        const scanner = type === 'Station' ? qrScannerStation : qrScannerBarcode;
        const readerId = type === 'Station' ? 'readerStation' : 'readerBarcode';

        document.getElementById(modalId).classList.add('hidden');

        if (scanner) {
            scanner.stop().then(() => {
                scanner.clear();
                document.getElementById(readerId).innerHTML = '';
                if (type === 'Station') qrScannerStation = null;
                if (type === 'Barcode') qrScannerBarcode = null;
            }).catch(err => console.error("Stop error:", err));
        }
    }

    function showModalAlert(message) {
        document.getElementById('stationAlertText').innerHTML = message;
        document.getElementById('stationAlertModal').classList.remove('hidden');
    }

    function closeModalAlert() {
        document.getElementById('stationAlertModal').classList.add('hidden');
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
    </script>
</x-app-layout>