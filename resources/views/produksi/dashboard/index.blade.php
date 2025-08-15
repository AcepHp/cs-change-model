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
                {{ __('Dashboard Produksi') }}
            </h2>
        </div>
    </x-slot>

    <div x-data="{ openModal: false, deleteUrl: '' }" class="py-6">
        <!-- Blur Background Saat Modal Aktif -->
        <div x-show="openModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-40 bg-black bg-opacity-30 backdrop-blur-sm"></div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @php
                $cards = [
                [
                'label' => 'Total Checksheet Hari Ini',
                'value' => number_format($checksheetToday),
                'color' => 'bg-green-500',
                // Icon: Clipboard Document Check
                'icon' => 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0
                0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8
                0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08
                1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621
                0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504
                1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75',
                ],
                [
                'label' => 'Checksheet Shift 1 Hari Ini',
                'value' => number_format($checksheetShift1),
                'color' => 'bg-yellow-500',
                // Icon: Clock
                'icon' => 'M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12
                18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75
                3.75 0 0 1 7.5 0Z',
                ],
                [

                'label' => 'Total Shift 2 Hari Ini',
                'value' => number_format($checksheetShift2),
                'color' => 'bg-amber-500',
                // Icon: Document Text
                'icon' => 'M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75
                0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0
                9.002-5.998Z',
                ],
                [
                'label' => 'Total Divalidasi Quality Hari Ini',
                'value' => number_format($totalQualityValidated),
                'color' => 'bg-purple-500',
                // Icon: Check Badge / User Identification
                'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296
                3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0
                1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745
                3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068
                1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
                ],

                ];
                @endphp

                @foreach ($cards as $card)
                <div class="p-5 rounded-xl ring-1 ring-gray-200 bg-white hover:shadow transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $card['color'] }}">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="{{ $card['icon'] }}" />
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-gray-500 text-xs mb-1">{{ $card['label'] }}</div>
                            <div class="text-2xl font-semibold text-gray-800">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Data Table Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <!-- Table Header -->
                        <div
                            class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h3 class="text-lg font-semibold text-gray-900">Data Checksheet Hari Ini</h3>
                            <form method="GET" class="flex items-center space-x-2">
                                <label for="shift" class="text-sm font-medium text-gray-700">Shift:</label>
                                <select name="shift" id="shift" onchange="this.form.submit()"
                                    class="min-w-[150px] px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition duration-150 ease-in-out">
                                    <option value="">Semua Shift</option>
                                    <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1</option>
                                    <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2</option>
                                </select>
                            </form>
                        </div>

                        <!-- Table Content -->
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
                                            Admin</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($logTableData as $i => $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $logTableData->firstItem() + $i }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Shift {{ $row->shift }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->area }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->line }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $row->frontView }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <p class="text-gray-500 text-sm">Tidak ada data checksheet hari ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                {{ $logTableData->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <!-- Chart Header -->
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Status Produksi</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                @if(request('shift'))
                                Data Shift {{ request('shift') }} Hari Ini
                                @else
                                Semua Data Hari Ini
                                @endif
                            </p>
                        </div>

                        <!-- Chart Content -->
                        <div class="p-6">
                            <div class="relative h-64 mb-4">
                                <canvas id="okNgPieChart"></canvas>
                            </div>

                            <!-- Chart Stats -->
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-green-600">{{ $okCount }}</div>
                                    <div class="text-sm text-gray-600">OK</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-red-600">{{ $ngCount }}</div>
                                    <div class="text-sm text-gray-600">NG</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $okCount + $ngCount }}</div>
                                    <div class="text-sm text-gray-600">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Data Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <!-- Header with Filters -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 lg:mb-0">Data Checksheet Total</h3>

                        <!-- Filter Section -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <select id="area-filter"
                                class="min-w-[150px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}
                                </option>
                                @endforeach
                            </select>

                            <select id="line-filter"
                                class="min-w-[150px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Line</option>
                                @foreach($lines as $line)
                                <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>{{ $line }}
                                </option>
                                @endforeach
                            </select>

                            <select id="frontview-filter"
                                class="min-w-[150px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Model</option>
                                @foreach($frontViews as $frontView)
                                <option value="{{ $frontView }}" {{ request('frontview') == $frontView ? 'selected' : '' }}>
                                    {{ $frontView }}</option>
                                @endforeach
                            </select>

                            <select id="shift-filter"
                                class="min-w-[150px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Shift</option>
                                <option value="1" {{ request('shift_filter') == '1' ? 'selected' : '' }}>Shift 1
                                </option>
                                <option value="2" {{ request('shift_filter') == '2' ? 'selected' : '' }}>Shift 2
                                </option>
                            </select>

                            <input type="date" id="date-filter" value="{{ request('date') }}"
                                class="min-w-[150px] h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">

                            <button type="button" id="reset-filters"
                                class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto w-full">
                    <div id="table-container">
                        @include('produksi.dashboard.partials.log_detail_table', ['totalTableData' => $totalTableData])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Production Status Chart
        const ctx = document.getElementById('okNgPieChart').getContext('2d');
        const okCount = @json($okCount);
        const ngCount = @json($ngCount);
        const total = okCount + ngCount;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['OK', 'NG'],
                datasets: [{
                    label: 'Status Produksi',
                    data: [okCount, ngCount],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444'
                    ],
                    borderColor: [
                        '#ffffff',
                        '#ffffff'
                    ],
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                    0;
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        cornerRadius: 6,
                        padding: 10
                    }
                },
                cutout: '65%',
                animation: {
                    animateRotate: true,
                    duration: 800
                }
            }
        });
    });

    $(document).ready(function() {
        let filterTimeout;

        function loadFilteredData() {
            const area = $('#area-filter').val();
            const line = $('#line-filter').val();
            const frontview = $('#frontview-filter').val();
            const shift = $('#shift-filter').val();
            const date = $('#date-filter').val();

            $('#table-container').addClass('opacity-50 pointer-events-none');

            $.ajax({
                url: "{{ route('produksi.dashboard') }}",
                type: 'GET',
                data: {
                    area: area || null,
                    line: line || null,
                    frontview: frontview || null,
                    shift_filter: shift || null,
                    date: date || null
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#table-container').removeClass('opacity-50 pointer-events-none');
                    $('#table-container').html(response);
                },
                error: function(xhr, status, error) {
                    $('#table-container').removeClass('opacity-50 pointer-events-none');

                    $('#table-container').html(`
                        <div class="p-4 bg-red-50 border border-red-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Gagal memuat data</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>${error}</p>
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" onclick="location.reload()" class="bg-red-100 px-2 py-1.5 rounded-md text-sm font-medium text-red-800 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Refresh Halaman
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                }
            });
        }

        $('#area-filter, #line-filter, #frontview-filter, #shift-filter, #date-filter').on('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(loadFilteredData, 300);
        });

        $('#reset-filters').on('click', function() {
            $('#area-filter, #line-filter, #frontview-filter, #shift-filter').val('');
            $('#date-filter').val('');
            loadFilteredData();
        });
    });
    </script>
    <script>
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
