<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Checksheet Data Export</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        margin: 0;
        padding: 20px;
        color: #000;
        background-color: #fff;
    }

    .header-container {
        border-bottom: 2px solid #000;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .company-info h1 {
        font-size: 18px;
        margin: 0;
        font-weight: bold;
        color: #000;
    }

    .company-subtitle {
        font-size: 10px;
        margin-top: 2px;
        font-style: italic;
        color: #333;
    }

    .company-address {
        font-size: 9px;
        color: #444;
        line-height: 1.3;
    }

    .filters {
        border: 1px solid #999;
        padding: 10px 15px;
        margin-bottom: 15px;
        font-size: 9px;
        border-radius: 4px;
    }

    .filters h3 {
        margin: 0 0 8px 0;
        font-size: 10px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 4px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 5px 15px;
    }

    .filter-label {
        font-weight: bold;
        color: #000;
    }

    .summary {
        text-align: right;
        margin-bottom: 10px;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
    }

    th,
    td {
        border: 1px solid #888;
        padding: 5px;
        vertical-align: top;
    }

    th {
        background-color: #000;
        color: white;
        font-weight: bold;
        text-align: center;
    }

    .text-center {
        text-align: center;
    }

    .status-ok,
    .status-ng,
    .status-pending {
        font-weight: bold;
        font-size: 8px;
        padding: 2px 4px;
        border: 1px solid #555;
        border-radius: 3px;
        display: inline-block;
    }

    .status-ok {
        color: #000;
    }

    .status-ng {
        color: #000;
    }

    .status-pending {
        color: #000;
        font-style: italic;
    }

    .footer {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-size: 8px;
        color: #555;
    }

    .no-data {
        text-align: center;
        padding: 30px;
        color: #555;
        font-style: italic;
    }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="logo-section">
            <div class="company-info">
                <h1>PT ASTRA VISTEON INDONESIA</h1>
                <p class="company-subtitle">Automotive Cockpit Electronics Manufacturer</p>
                <div class="company-address">
                    Jl. Lanbau RT 05/10 Kel. Karang Asem Barat Kec. Citeureup, Bogor, Indonesia, 16810<br>
                    Email: marketing@astra-visteon.com
                </div>
            </div>
        </div>
    </div>

    @if(!empty(array_filter($filters)))
    <div class="filters">
        <h3>Applied Filters</h3>
        <div class="filter-grid">
            @if($filters['area'] ?? null)
            <div><span class="filter-label">Area:</span> {{ $filters['area'] }}</div>
            @endif
            @if($filters['line'] ?? null)
            <div><span class="filter-label">Line:</span> {{ $filters['line'] }}</div>
            @endif
            @if($filters['model'] ?? null)
            <div><span class="filter-label">Model:</span> {{ $filters['model'] }}</div>
            @endif
            @if($filters['station'] ?? null)
            <div><span class="filter-label">Station:</span> {{ $filters['station'] }}</div>
            @endif
            @if($filters['date_from'] ?? null)
            <div><span class="filter-label">Date From:</span>
                {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}</div>
            @endif
            @if($filters['date_to'] ?? null)
            <div><span class="filter-label">Date To:</span>
                {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</div>
            @endif
            @if($filters['shift'] ?? null)
            <div><span class="filter-label">Shift:</span> {{ $filters['shift'] }}</div>
            @endif
            @if($filters['prod_status'] ?? null)
            <div><span class="filter-label">Prod Status:</span> {{ $filters['prod_status'] }}</div>
            @endif
            @if($filters['quality_status'] ?? null)
            <div><span class="filter-label">Quality Status:</span>
                {{ $filters['quality_status'] === 'pending' ? 'Pending' : $filters['quality_status'] }}</div>
            @endif
        </div>
    </div>
    @endif

    <div class="summary">
        Total Records: {{ number_format($totalRecords) }}
    </div>

    @if($data->count() > 0)
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Shift</th>
                <th>Area</th>
                <th>Line</th>
                <th>Model</th>
                <th>Station</th>
                <th>Check Item</th>
                <th>Standard</th>
                <th>Result Image</th> <!-- Tambahan -->
                <th>Prod Status</th>
                <th>Prod Checked By</th> <!-- Tambahan -->
                <th>Quality Status</th>
                <th>Quality Checked By</th> <!-- Tambahan -->
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->log->date ?? now())->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->log->shift ?? '-' }}</td>
                <td>{{ $item->log->area ?? '-' }}</td>
                <td>{{ $item->log->line ?? '-' }}</td>
                <td>{{ $item->log->partModelRelation->frontView ?? $item->log->model ?? '-' }}</td>
                <td>{{ $item->station ?? '-' }}</td>

                {{-- Check Item --}}
                <td>
                    @php
                        $src = null;
                        if ($item->check_item && Storage::disk('public')->exists($item->check_item)) {
                            $file = Storage::disk('public')->get($item->check_item);
                            $mime = Storage::disk('public')->mimeType($item->check_item);
                            $src = 'data:' . $mime . ';base64,' . base64_encode($file);
                        }
                    @endphp

                    @if ($src)
                        <img src="{{ $src }}" alt="Check Item" style="max-width: 80px; height: auto;">
                    @else
                        {{ $item->check_item ?: '-' }}
                    @endif
                </td>

                <td>{{ $item->standard ?? '-' }}</td>

                {{-- Result Image --}}
                <td class="text-center">
                    @php
                        $src = null;
                        if ($item->resultImage && Storage::disk('public')->exists($item->resultImage)) {
                            $file = Storage::disk('public')->get($item->resultImage);
                            $mime = Storage::disk('public')->mimeType($item->resultImage);
                            $src = 'data:' . $mime . ';base64,' . base64_encode($file);
                        }
                    @endphp

                    @if ($src)
                        <img src="{{ $src }}" alt="Result" style="max-width: 80px; height: auto;">
                    @else
                        -
                    @endif
                </td>


                {{-- Prod Status --}}
                <td class="text-center">
                    @if($item->prod_status === 'OK')
                    <span class="status-ok">OK</span>
                    @elseif($item->prod_status === 'NG')
                    <span class="status-ng">NG</span>
                    @else
                    -
                    @endif
                </td>

                {{-- Prod Checked By --}}
                <td class="text-center">{{ $item->prod_checked_by ?? '-' }}</td>

                {{-- Quality Status --}}
                <td class="text-center">
                    @if($item->quality_status === 'OK')
                    <span class="status-ok">OK</span>
                    @elseif($item->quality_status === 'NG')
                    <span class="status-ng">NG</span>
                    @else
                    <span class="status-pending">Pending</span>
                    @endif
                </td>

                {{-- Quality Checked By --}}
                <td class="text-center">{{ $item->quality_checked_by ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @else
    <div class="no-data">No data found with the applied filters.</div>
    @endif

</body>

</html>