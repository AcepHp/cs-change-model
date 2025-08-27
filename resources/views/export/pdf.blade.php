<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Checksheet Change Model</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        margin: 0;
        padding: 20px;
        color: #000;
        background-color: #fff;
    }

    /* === KOP LAPORAN === */
    .kop-table {
        width: 100%;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .kop-table td {
        border: 1px solid #000;
        vertical-align: middle;
        padding: 5px;
    }

    .kop-logo {
        text-align: center;
        width: 25%;
    }

    .kop-logo img {
        max-height: 50px;
        width: auto;
    }

    .kop-title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        width: 50%;
    }

    .kop-info {
        width: 25%;
        font-size: 11px;
        text-align: center;
    }

    .kop-info div {
        padding: 5px;
    }

    /* === BOX 4 KOLOM === */
    .box-table {
        width: 100%;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 10px;
        text-align: center;
        color: #000;
    }

    .box-table th,
    .box-table td {
        border: 1px solid #000;
        padding: 6px;
        width: 25%;
        color: #000;
    }

    .box-table th {
        background-color: #f2f2f2;
        font-weight: bold;
        color: #000;
    }

    /* === TABLE DATA === */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
    }

    th,
    td {
        border: 1px solid #888;
        padding: 5px;
        vertical-align: middle;
        color: #000;
    }

    th {
        background-color: #f2f2f2;
        color: #000;
        font-weight: bold;
        text-align: center;
    }


    .text-center {
        text-align: center;
    }



    .no-data {
        text-align: center;
        padding: 30px;
        color: #555;
        font-style: italic;
    }

    .image-cell img {
        max-width: 80px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    {{-- === HEADER (KOP) === --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="AVI Logo">
                @endif
            </td>
            <td class="kop-title">
                CHECKSHEET CHANGE MODEL
            </td>
            <td class="kop-info">
                <div>
                    Tanggal:
                    @if($filters['date'] ?? false)
                    {{ \Carbon\Carbon::parse($filters['date'])->format('d/m/Y') }}
                    @else
                    {{ now()->format('d/m/Y') }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- === KOTAK SHIFT, AREA, LINE, MODEL === --}}
    <table class="box-table">
        <tr>
            <th>Shift</th>
            <th>Area</th>
            <th>Line</th>
            <th>Model</th>
        </tr>
        <tr>
            <td>{{ $filters['shift'] ?? '-' }}</td>
            <td>{{ $filters['area'] ?? '-' }}</td>
            <td>{{ $filters['line'] ?? '-' }}</td>
            <td>
                {{ $processedData->first()->log->partModelRelation->frontView ?? $filters['model'] ?? '-' }}
            </td>

        </tr>
    </table>

    {{-- === SUMMARY === --}}
    <div class="summary">
        Total Records: {{ number_format($totalRecords) }}
    </div>

    {{-- === TABLE DATA === --}}
    @if($processedData->count() > 0)
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
                <th>Result Image</th>
                <th>Prod Status</th>
                <th>Prod Checked By</th>
                <th>Quality Status</th>
                <th>Quality Checked By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedData as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->log->date ?? now())->format('d/m/Y') }}</td>
                <td class="text-center">{{ $item->log->shift ?? '-' }}</td>
                <td>{{ $item->log->area ?? '-' }}</td>
                <td>{{ $item->log->line ?? '-' }}</td>
                <td>{{ $item->log->partModelRelation->frontView ?? $item->log->model ?? '-' }}</td>
                <td>{{ $item->station ?? '-' }}</td>

                {{-- Check Item (image or text) --}}
                <td class="image-cell">
                    @if($item->check_item_base64)
                    <img src="{{ $item->check_item_base64 }}" alt="Check Item">
                    @else
                    {{ $item->check_item ?: '-' }}
                    @endif
                </td>

                <td>{{ $item->standard ?? '-' }}</td>

                {{-- Result Image --}}
                <td class="image-cell">
                    @if($item->result_image_base64)
                    <img src="{{ $item->result_image_base64 }}" alt="Result">
                    @else
                    -
                    @endif
                </td>

                {{-- Prod Status --}}
                <td class="text-center">
                    @if($item->prod_status === 'OK')
                    <span>OK</span>
                    @elseif($item->prod_status === 'NG')
                    <span>NG</span>
                    @else
                    -
                    @endif
                </td>

                <td class="text-center">{{ $item->prod_checked_by ?? '-' }}</td>

                {{-- Quality Status --}}
                <td class="text-center">
                    @if($item->quality_status === 'OK')
                    <span>OK</span>
                    @elseif($item->quality_status === 'NG')
                    <span>NG</span>
                    @else
                    <span>Pending</span>
                    @endif
                </td>

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