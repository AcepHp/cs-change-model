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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Checksheet Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Checksheet Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($checksheetToday) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shift 1 Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Shift 1 Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($checksheetShift1) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shift 2 Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Shift 2 Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($checksheetShift2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quality Validated Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Divalidasi Quality Hari Ini</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalQualityValidated) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Data Table Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <!-- Table Header -->
                        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">Data Checksheet Hari Ini</h3>
                            <form method="GET" class="flex items-center space-x-2">
                                <select name="shift" id="shift" onchange="this.form.submit()" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Line</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($logTableData as $i => $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $i + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Shift {{ $row->shift }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->area }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->line }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->model }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($row->status)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $row->status }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-gray-500 text-sm">Tidak ada data checksheet hari ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                        <div class="flex flex-wrap gap-3">
                            <select id="area-filter" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Area</option>
                                @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                                @endforeach
                            </select>

                            <select id="line-filter" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Line</option>
                                @foreach($lines as $line)
                                <option value="{{ $line }}" {{ request('line') == $line ? 'selected' : '' }}>{{ $line }}</option>
                                @endforeach
                            </select>

                            <select id="model-filter" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Model</option>
                                @foreach($models as $model)
                                <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>{{ $model }}</option>
                                @endforeach
                            </select>

                            <select id="shift-filter" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Semua Shift</option>
                                <option value="1" {{ request('shift_filter') == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ request('shift_filter') == '2' ? 'selected' : '' }}>Shift 2</option>
                            </select>

                            <input type="date" id="date-filter" value="{{ request('date') }}" class="block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">

                            <button type="button" id="reset-filters" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
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
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
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
            const model = $('#model-filter').val();
            const shift = $('#shift-filter').val();
            const date = $('#date-filter').val();

            $('#table-container').addClass('opacity-50 pointer-events-none');

            $.ajax({
                url: "{{ route('produksi.dashboard.filter') }}",
                type: 'GET',
                data: {
                    area: area || null,
                    line: line || null,
                    model: model || null,
                    shift_filter: shift || null,
                    date: date || null
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

        $('#area-filter, #line-filter, #model-filter, #shift-filter, #date-filter').on('change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(loadFilteredData, 300);
        });

        $('#reset-filters').on('click', function() {
            $('#area-filter, #line-filter, #model-filter, #shift-filter').val('');
            $('#date-filter').val('');
            loadFilteredData();
        });
    });
    </script>
</x-app-layout>
