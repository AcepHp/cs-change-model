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

                <form id="checksheetForm" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="area" value="{{ $area }}">
                    <input type="hidden" name="line" value="{{ $line }}">
                    <input type="hidden" name="model" value="{{ $model }}">
                    <input type="hidden" name="shift" value="{{ $shift }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    @php $grouped = $results->groupBy('station'); @endphp

                    @foreach($grouped as $station => $items)
                        <div class="mb-6 border rounded-lg shadow-sm">
                            <div class="flex justify-between items-center px-4 py-3 bg-gray-100 border-b">
                                <div class="font-semibold text-lg text-gray-700">Station: {{ $station }}</div>
                                <button type="button" onclick="openStationScanner('{{ $station }}')" class="inline-flex items-center px-4 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Scan Station
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="border px-3 py-2">{{ $item->list }}</td>
                                                <td class="border px-3 py-2">{{ $item->check_item }}</td>
                                                <td class="border px-3 py-2">{{ $item->standard }}</td>
                                                <td class="border px-3 py-2" id="trigger-{{ $item->id }}">{{ $item->trigger }}</td>
                                                <td class="border px-3 py-2">
                                                    @php $actualType = strtolower($item->actual); @endphp
                                                    @if($actualType === 'scan' || $actualType === 'containscan')
                                                        <input type="hidden" name="actual[{{ $item->id }}]" id="barcode-value-{{ $item->id }}" value="" disabled>
                                                        <button type="button" onclick="openBarcodeScanner('{{ $item->id }}', '{{ $actualType }}')" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700" disabled>
                                                            Scan Barcode
                                                        </button>
                                                    @elseif($actualType === 'check')
                                                        <select name="actual[{{ $item->id }}]" class="border rounded px-2 py-1 text-sm w-full" onchange="updateCheckStatus('{{ $item->id }}')" disabled>
                                                            <option value="">Pilih</option>
                                                            <option value="OK">OK</option>
                                                            <option value="NG">NG</option>
                                                        </select>
                                                    @else
                                                        <span class="text-gray-500 italic">-</span>
                                                    @endif
                                                </td>
                                                <td class="border px-3 py-2">
                                                    <span id="barcode-value-display-{{ $item->id }}" class="text-gray-600 italic">Belum discan</span>
                                                </td>
                                                <td class="border px-3 py-2">
                                                    <span id="status-{{ $item->id }}" class="font-semibold text-gray-700">-</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    @if(!$isSubmitted)
                        <div class="mt-8 text-right">
                            <x-primary-button id="submitBtn" type="submit" class="px-6 py-2 opacity-50 cursor-not-allowed" disabled>
                                {{ __('Simpan Semua') }}
                            </x-primary-button>
                        </div>
                    @else
                        <div class="mt-8 flex items-center justify-between">
                            <div class="text-green-600 font-semibold">
                                Data sudah disubmit. Form terkunci.
                            </div>
                            <x-primary-button type="button" onclick="window.location.href='{{ route('operator.inputChecksheet.summary', ['id_log' => $id_log]) }}'">
                                Lihat Score
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Scan Station --}}
    <div id="scannerStationModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-4 w-full max-w-md relative">
            <h2 class="text-lg font-semibold mb-4">Scan QR Station</h2>
            <div id="readerStation" class="w-full"></div>
            <button onclick="closeScanner('Station')" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
        </div>
    </div>

    {{-- Modal Scan Barcode --}}
    <div id="scannerBarcodeModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-4 w-full max-w-md relative">
            <h2 class="text-lg font-semibold mb-4">Scan QR Barcode</h2>
            <div id="readerBarcode" class="w-full"></div>
            <button onclick="closeScanner('Barcode')" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
        </div>
    </div>

    {{-- Modal Alert Station --}}
    <div id="stationAlertModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md text-center">
            <h3 class="text-lg font-semibold mb-4 text-red-600">Station Tidak Cocok</h3>
            <p id="stationAlertText" class="mb-4 text-gray-700"></p>
            <button onclick="closeModalAlert()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">OK</button>
        </div>
    </div>

    {{-- Include HTML5Qrcode --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let qrScannerStation, qrScannerBarcode;
        let scannedStations = {};

        function openStationScanner(station) {
            document.getElementById('scannerStationModal').classList.remove('hidden');
            if (!qrScannerStation) {
                qrScannerStation = new Html5Qrcode("readerStation");
            }

            qrScannerStation.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                qrCodeMessage => {
                    if (qrCodeMessage.trim() === station.trim()) {
                        scannedStations[station] = true;

                        document.querySelectorAll(`[data-station="${station}"] input, [data-station="${station}"] select, [data-station="${station}"] button`).forEach(el => {
                            el.disabled = false;
                            el.classList.remove('opacity-50', 'cursor-not-allowed');
                        });

                        const allStations = [...new Set(Array.from(document.querySelectorAll('[data-station]')).map(el => el.getAttribute('data-station')))];

                        const allScanned = allStations.every(st => scannedStations[st]);

                        if (allScanned) {
                            const submitBtn = document.getElementById('submitBtn');
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    } else {
                        showModalAlert(`Scan: ${qrCodeMessage}<br>Diharapkan: ${station}`);
                    }

                    closeScanner('Station');
                },
                error => {}
            ).catch(err => console.error("Camera error:", err));
        }

        function openBarcodeScanner(itemId, actualType) {
            document.getElementById('scannerBarcodeModal').classList.remove('hidden');
            if (!qrScannerBarcode) {
                qrScannerBarcode = new Html5Qrcode("readerBarcode");
            }

            qrScannerBarcode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                qrCodeMessage => {
                    const trigger = document.getElementById(`trigger-${itemId}`).innerText.trim();
                    const input = document.getElementById(`barcode-value-${itemId}`);
                    const display = document.getElementById(`barcode-value-display-${itemId}`);
                    const status = document.getElementById(`status-${itemId}`);

                    input.value = qrCodeMessage;
                    display.innerText = qrCodeMessage;

                    if (actualType === 'scan') {
                        status.innerText = (qrCodeMessage === trigger) ? 'OK' : 'NG';
                    } else if (actualType === 'containscan') {
                        status.innerText = qrCodeMessage.includes(trigger) ? 'OK' : 'NG';
                    }

                    closeScanner('Barcode');
                },
                error => {}
            ).catch(err => console.error("Camera error:", err));
        }

        function updateCheckStatus(itemId) {
            const select = document.querySelector(`select[name="actual[${itemId}]"]`);
            const status = document.getElementById(`status-${itemId}`);
            status.innerText = select.value || '-';
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
    </script>
</x-app-layout>
