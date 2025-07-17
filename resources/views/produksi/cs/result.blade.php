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

                    @php
                        $grouped = $results->groupBy('station');
                    @endphp

                    @foreach($grouped as $station => $items)
                        <div class="mb-6 border rounded-lg shadow-sm">
                            <div class="flex justify-between items-center px-4 py-3 bg-gray-100 border-b">
                                <div class="font-semibold text-lg text-gray-700">Station: {{ $station }}</div>
                                <button type="button" onclick="openScanner('{{ $station }}')" class="inline-flex items-center px-4 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">
                                    Scan Station
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="table-auto w-full text-sm text-left text-gray-700">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="border px-3 py-2">List</th>
                                            <th class="border px-3 py-2">Check Item</th>
                                            <th class="border px-3 py-2">Standard</th>
                                            <th class="border px-3 py-2">Trigger</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td class="border px-3 py-2">{{ $item->list }}</td>
                                                <td class="border px-3 py-2">{{ $item->check_item }}</td>
                                                <td class="border px-3 py-2">{{ $item->standard }}</td>
                                                <td class="border px-3 py-2">{{ $item->trigger }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    {{-- Tombol --}}
                    @if(!$isSubmitted)
                        <div class="mt-8 text-right">
                            <x-primary-button type="submit" class="px-6 py-2 opacity-50 cursor-not-allowed" disabled>
                                {{ __('Simpan Semua') }}
                            </x-primary-button>
                        </div>
                    @else
                        <div class="mt-8 flex items-center justify-between">
                            <div class="text-green-600 font-semibold">
                                Data sudah disubmit. Form terkunci.
                            </div>
                            <x-primary-button type="button"
                                onclick="window.location.href='{{ route('operator.inputChecksheet.summary', ['id_log' => $id_log]) }}'">
                                Lihat Score
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Modal HTML5Qrcode --}}
    <div id="scannerModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-4 w-full max-w-md relative">
            <h2 class="text-lg font-semibold mb-4">Scan QR Station</h2>
            <div id="reader" class="w-full"></div>
            <button onclick="closeScanner()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900">&times;</button>
        </div>
    </div>

    {{-- Include HTML5Qrcode Library --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrcodeScanner;
        function openScanner(station) {
            document.getElementById('scannerModal').classList.remove('hidden');
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5Qrcode("reader");
            }

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: 250
                },
                qrCodeMessage => {
                    alert(`Scan result for station ${station}: ${qrCodeMessage}`);
                    closeScanner();
                },
                errorMessage => {
                    // console.log(`Scan error: ${errorMessage}`);
                }
            ).catch(err => {
                console.error("Camera start error", err);
            });
        }

        function closeScanner() {
            document.getElementById('scannerModal').classList.add('hidden');
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                }).catch(err => {
                    console.error("Camera stop error", err);
                });
            }
        }
    </script>
</x-app-layout>
