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
    }

    .header-container {
        border-bottom: 3px solid #1e40af;
        margin-bottom: 25px;
        padding-bottom: 15px;
        position: relative;
    }

    .logo-section {
        display: table;
        width: 100%;
        margin-bottom: 10px;
    }

    .logo-left {
        display: table-cell;
        width: 80px;
        vertical-align: middle;
    }

    .logo-left img {
        width: 70px;
        height: auto;
    }

    .company-info {
        display: table-cell;
        vertical-align: middle;
        padding-left: 15px;
    }

    .company-name {
        font-size: 20px;
        font-weight: bold;
        color: #1e40af;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .company-subtitle {
        font-size: 11px;
        color: #666;
        margin: 2px 0;
        font-style: italic;
    }

    .company-address {
        font-size: 9px;
        color: #555;
        margin: 5px 0 0 0;
        line-height: 1.3;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
    }

    .header h1 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .header p {
        margin: 5px 0;
        color: #666;
    }

    .filters {
        background-color: #eef2ff;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-left: 5px solid #1e40af;
        border-radius: 5px;
        font-size: 9px;
    }

    .filters h3 {
        margin: 0 0 10px 0;
        font-size: 11px;
        color: #1e40af;
        font-weight: bold;
        border-bottom: 1px dashed #ccc;
        padding-bottom: 5px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px 20px;
    }

    .filter-item {
        color: #111827;
        margin-bottom: 5px;
    }

    .filter-label {
        font-weight: bold;
        color: #374151;

    }

    .summary {
        text-align: right;
        margin-bottom: 15px;
        font-weight: bold;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 6px;
        text-align: left;
        vertical-align: top;
    }

    th {
        background-color: #4F46E5;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 9px;
    }

    td {
        font-size: 8px;
    }

    .status-ok {
        background-color: #d1fae5;
        color: #065f46;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
    }

    .status-ng {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
        padding: 2px 6px;
        border-radius: 3px;
        font-weight: bold;
    }

    .footer {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-size: 8px;
        color: #666;
    }

    .page-break {
        page-break-after: always;
    }

    .text-center {
        text-align: center;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #666;
        font-style: italic;
    }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="logo-section">
            <div class="company-info">
                <h1 class="company-name">PT ASTRA VISTEON INDONESIA</h1>
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
            <div class="filter-item">
                <span class="filter-label">Area :</span> {{ $filters['area'] }}
            </div>
            @endif
            @if($filters['line'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Line :</span> {{ $filters['line'] }}
            </div>
            @endif
            @if($filters['model'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Model :</span> {{ $filters['model'] }}
            </div>
            @endif
            @if($filters['station'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Station :</span> {{ $filters['station'] }}
            </div>
            @endif
            @if($filters['date_from'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Date From :</span>
                {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}
            </div>
            @endif
            @if($filters['date_to'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Date To :</span>
                {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
            </div>
            @endif
            @if($filters['shift'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Shift:</span> {{ $filters['shift'] }}
            </div>
            @endif
            @if($filters['prod_status'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Prod Status:</span> {{ $filters['prod_status'] }}
            </div>
            @endif
            @if($filters['quality_status'] ?? null)
            <div class="filter-item">
                <span class="filter-label">Quality Status:</span>
                {{ $filters['quality_status'] === 'pending' ? 'Pending' : $filters['quality_status'] }}
            </div>
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
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 5%;">Shift</th>
                <th style="width: 8%;">Area</th>
                <th style="width: 8%;">Line</th>
                <th style="width: 10%;">Model</th>
                <th style="width: 8%;">Station</th>
                <th style="width: 5%;">List</th>
                <th style="width: 20%;">Check Item</th>
                <th style="width: 15%;">Standard</th>
                <th style="width: 5%;">Prod Status</th>
                <th style="width: 5%;">Quality Status</th>
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
                <td>{{ $item->log->model ?? '-' }}</td>
                <td>{{ $item->station ?? '-' }}</td>
                <td>{{ $item->list ?? '-' }}</td>
                <td>{{ $item->check_item ?? '-' }}</td>
                <td>{{ $item->standard ?? '-' }}</td>
                <td class="text-center">
                    @if($item->prod_status === 'OK')
                    <span class="status-ok">OK</span>
                    @elseif($item->prod_status === 'NG')
                    <span class="status-ng">NG</span>
                    @else
                    -
                    @endif
                </td>
                <td class="text-center">
                    @if($item->quality_status === 'OK')
                    <span class="status-ok">OK</span>
                    @elseif($item->quality_status === 'NG')
                    <span class="status-ng">NG</span>
                    @else
                    <span class="status-pending">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No data found with the applied filters.</p>
    </div>
    @endif

    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT}
    </div>
</body>

</html>