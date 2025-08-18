<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checksheet Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 120px;
            font-weight: bold;
            padding: 3px 0;
        }
        
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        
        .data-table td {
            font-size: 10px;
        }
        
        .status-ok {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-ng {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-na {
            background-color: #e2e3e5;
            color: #383d41;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-unchecked {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        
        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN CHECKSHEET PRODUKSI</h1>
        <h2>{{ $filters['area'] }} - {{ $filters['line'] }} - {{ $filters['model'] }}</h2>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Shift:</div>
            <div class="info-value">{{ $filters['shift'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $filters['date'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Area:</div>
            <div class="info-value">{{ $filters['area'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Line:</div>
            <div class="info-value">{{ $filters['line'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Model:</div>
            <div class="info-value">{{ $filters['model'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Total Item:</div>
            <div class="info-value">{{ $logDetails->count() }} item</div>
        </div>
    </div>

    <!-- Fixed summary calculation to use logDetails directly -->
    <div class="summary-box">
        <div class="summary-title">Ringkasan Status:</div>
        @php
            $statusCounts = [
                'OK' => 0,
                'NG' => 0,
                'N/A' => 0,
                'Unchecked' => 0
            ];
            
            foreach($logDetails as $logDetail) {
                if ($logDetail->prod_status) {
                    if (isset($statusCounts[$logDetail->prod_status])) {
                        $statusCounts[$logDetail->prod_status]++;
                    } else {
                        $statusCounts['Unchecked']++;
                    }
                } else {
                    $statusCounts['Unchecked']++;
                }
            }
        @endphp
        
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 25%; padding: 5px;">
                    <strong>OK:</strong> {{ $statusCounts['OK'] }} item
                </div>
                <div style="display: table-cell; width: 25%; padding: 5px;">
                    <strong>NG:</strong> {{ $statusCounts['NG'] }} item
                </div>
                <div style="display: table-cell; width: 25%; padding: 5px;">
                    <strong>N/A:</strong> {{ $statusCounts['N/A'] }} item
                </div>
                <div style="display: table-cell; width: 25%; padding: 5px;">
                    <strong>Belum Dicek:</strong> {{ $statusCounts['Unchecked'] }} item
                </div>
            </div>
        </div>
    </div>

    <!-- Fixed data table to use logDetails directly -->
    @if($logDetails->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Station</th>
                <th style="width: 25%;">Check Item</th>
                <th style="width: 20%;">Standard</th>
                <th style="width: 15%;">List</th>
                <th style="width: 13%;">Scan Result</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logDetails as $index => $logDetail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $logDetail->station }}</td>
                    <td>{{ $logDetail->check_item }}</td>
                    <td>{{ $logDetail->standard }}</td>
                    <td>{{ $logDetail->list }}</td>
                    <td>{{ $logDetail->scanResult ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($logDetail->prod_status)
                            <span class="status-{{ strtolower($logDetail->prod_status) }}">
                                {{ $logDetail->prod_status }}
                            </span>
                        @else
                            <span class="status-unchecked">Belum Dicek</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        Tidak ada data checksheet untuk filter yang dipilih.
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Digenerate pada: {{ $generatedAt }}</p>
        <p>Sistem Checksheet Produksi</p>
    </div>
</body>
</html>
