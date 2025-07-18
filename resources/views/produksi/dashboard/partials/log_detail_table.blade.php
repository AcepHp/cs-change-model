<div class="w-full overflow-x-hidden">
    <table class="w-full text-sm text-left text-gray-700 whitespace-normal table-auto">
        <thead class="text-xs uppercase bg-gray-100 border-b border-gray-300 text-gray-600 ">
            <tr>
                <th class="px-2 py-3">#</th>
                <th class="px-2 py-3">Tanggal</th>
                <th class="px-2 py-3 w-[70px]">Shift</th>
                <th class="px-2 py-3">Area</th>
                <th class="px-2 py-3">Line</th>
                <th class="px-2 py-3">Model</th>
                <th class="px-2 py-3">Station</th>
                <th class="px-2 py-3">Check Item</th>
                <th class="px-2 py-3">Standard</th>
                <th class="px-2 py-3 text-center">Prod Status</th>
                <th class="px-2 py-3 text-center">Quality Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($totalTableData as $i => $item)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-2 py-3 font-semibold text-gray-800">
                    {{ ($totalTableData->currentPage() - 1) * $totalTableData->perPage() + $loop->iteration }}
                </td>
                <td class="px-2 py-3">{{ \Carbon\Carbon::parse($item->log->date ?? now())->format('d/m/Y') }}</td>
                <td class="px-2 py-3">
                    <span class="px-2 py-1 w-[70px] text-xs rounded-full bg-blue-100 text-blue-800 font-medium">
                        Shift {{ $item->log->shift ?? '-' }}
                    </span>
                </td>
                <td class="px-2 py-3">{{ $item->log->area ?? '-' }}</td>
                <td class="px-2 py-3">{{ $item->log->line ?? '-' }}</td>
                <td class="px-2 py-3">{{ $item->log->model ?? '-' }}</td>
                <td class="px-2 py-3">{{ $item->station }}</td>
                <td class="px-2 py-3 break-words">{{ $item->check_item }}</td>
                <td class="px-2 py-3 break-words">{{ $item->standard }}</td>
                <td class="px-2 py-3 text-center">
                    @if($item->actual === 'OK')
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                        OK
                    </span>
                    @elseif($item->actual === 'NG')
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                        NG
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                        {{ $item->prod_status }}
                    </span>
                    @endif
                </td>
                <td class="px-2 py-3 text-center">
                    @if($item->quality_status === 'OK')
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                        OK
                    </span>
                    @elseif($item->quality_status === 'NG')
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                        NG
                    </span>
                    @else
                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                        Pending
                    </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center px-6 py-10 text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm">Tidak ada data checksheet ditemukan</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>



<!-- Pagination -->
@if($totalTableData->hasPages())
<div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
    <div class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($totalTableData->onFirstPage())
            <span
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                Previous
            </span>
            @else
            <a href="{{ $totalTableData->previousPageUrl() }}"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                Previous
            </a>
            @endif

            @if ($totalTableData->hasMorePages())
            <a href="{{ $totalTableData->nextPageUrl() }}"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                Next
            </a>
            @else
            <span
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                Next
            </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    Menampilkan
                    <span class="font-medium">{{ $totalTableData->firstItem() }}</span>
                    sampai
                    <span class="font-medium">{{ $totalTableData->lastItem() }}</span>
                    dari
                    <span class="font-medium">{{ $totalTableData->total() }}</span>
                    hasil
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($totalTableData->onFirstPage())
                    <span aria-disabled="true" aria-label="Previous">
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5"
                            aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                    @else
                    <a href="{{ $totalTableData->previousPageUrl() }}" rel="prev"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                        aria-label="Previous">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($totalTableData->getUrlRange(1, $totalTableData->lastPage()) as $page => $url)
                    @if ($page == $totalTableData->currentPage())
                    <span aria-current="page">
                        <span
                            class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-blue-600 border border-blue-600 cursor-default leading-5">{{ $page }}</span>
                    </span>
                    @else
                    <a href="{{ $url }}"
                        class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
                        aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($totalTableData->hasMorePages())
                    <a href="{{ $totalTableData->nextPageUrl() }}" rel="next"
                        class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
                        aria-label="Next">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    @else
                    <span aria-disabled="true" aria-label="Next">
                        <span
                            class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md leading-5"
                            aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>
@endif

</div>