{{-- Enhanced preview table with better styling and empty state handling --}}
<div class="bg-white shadow-lg sm:rounded-lg overflow-hidden border border-gray-200">
    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Preview Data Checksheet
                </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">
                    Total: <span class="font-semibold text-blue-600">{{ $logDetails->count() }}</span> item checksheet
                </p>
                @if($log->id_log)
                    <p class="text-xs text-gray-500">Log ID: {{ $log->id_log }}</p>
                @else
                    <p class="text-xs text-red-500">Tidak ada log ditemukan</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Always show table structure, even when no data --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standard</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">List</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scan Result</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prod Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quality Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Checked At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @if($logDetails->count() > 0)
                    @foreach($logDetails as $index => $logDetail)
                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $logDetail->station }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                <div class="truncate" title="{{ $logDetail->check_item }}">{{ $logDetail->check_item }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                <div class="truncate" title="{{ $logDetail->standard }}">{{ $logDetail->standard }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $logDetail->list }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                    {{ $logDetail->scanResult ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($logDetail->prod_status == 'OK')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        OK
                                    </span>
                                @elseif($logDetail->prod_status == 'NG')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        NG
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                        </svg>
                                        Belum Dicek
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($logDetail->quality_status == 'OK')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        OK
                                    </span>
                                @elseif($logDetail->quality_status == 'NG')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        NG
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div class="text-xs">
                                        <span class="font-medium text-gray-500">Prod:</span> 
                                        <span class="text-gray-900">{{ $logDetail->prod_checked_by ?? '-' }}</span>
                                    </div>
                                    <div class="text-xs">
                                        <span class="font-medium text-gray-500">Quality:</span> 
                                        <span class="text-gray-900">{{ $logDetail->quality_checked_by ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="space-y-1">
                                    <div class="text-xs">
                                        {{ $logDetail->prod_checked_at ? $logDetail->prod_checked_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <div class="text-xs">
                                        {{ $logDetail->quality_checked_at ? $logDetail->quality_checked_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Enhanced empty state with better visual design --}}
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Data</h3>
                                <p class="text-gray-500 text-center max-w-md">
                                    Tidak ada data log detail untuk filter yang dipilih. 
                                    Pastikan data sudah diinput terlebih dahulu atau coba dengan filter yang berbeda.
                                </p>
                                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-sm text-blue-700">
                                        <strong>Filter yang dipilih:</strong><br>
                                        {{ $filters['area'] }} - {{ $filters['line'] }} - {{ $filters['model'] }} 
                                        (Shift {{ $filters['shift'] }}, {{ \Carbon\Carbon::parse($filters['date'])->format('d/m/Y') }})
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Added summary footer for better data overview --}}
    @if($logDetails->count() > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-wrap justify-between items-center text-sm text-gray-600">
                <div class="flex space-x-6">
                    @php
                        $okCount = $logDetails->where('prod_status', 'OK')->count();
                        $ngCount = $logDetails->where('prod_status', 'NG')->count();
                        $uncheckedCount = $logDetails->whereNull('prod_status')->count();
                    @endphp
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                        OK: {{ $okCount }}
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        NG: {{ $ngCount }}
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
                        Belum Dicek: {{ $uncheckedCount }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="font-medium">Total: {{ $logDetails->count() }} item</span>
                </div>
            </div>
        </div>
    @endif
</div>
