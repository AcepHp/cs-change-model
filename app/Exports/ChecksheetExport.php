<?php

namespace App\Exports;

use App\Models\LogDetailCs;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ChecksheetExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return LogDetailCs::select([
                'log_detail_cs.log_cs_id',
                'log_detail_cs.station', 
                'log_detail_cs.list',
                'log_detail_cs.check_item',
                'log_detail_cs.standard',
                'log_detail_cs.prod_status',
                'log_detail_cs.quality_status',
                'log_detail_cs.created_at',
                'log_cs.date',
                'log_cs.shift',
                'log_cs.area',
                'log_cs.line',
                'log_cs.model'
            ])
            ->join('log_cs', 'log_detail_cs.id_log', '=', 'log_cs.id_log')
            ->when($this->filters['area'] ?? null, function ($query, $area) {
                $query->where('log_cs.area', $area);
            })
            ->when($this->filters['line'] ?? null, function ($query, $line) {
                $query->where('log_cs.line', $line);
            })
            ->when($this->filters['model'] ?? null, function ($query, $model) {
                $query->where('log_cs.model', $model);
            })
            ->when($this->filters['date'] ?? null, function ($query, $date) {
                $query->whereDate('log_cs.date', $date);
            })
            ->when($this->filters['shift'] ?? null, function ($query, $shift) {
                $query->where('log_cs.shift', $shift);
            })
            ->orderBy('log_detail_cs.list', 'asc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Shift',
            'Area',
            'Line',
            'Model',
            'Station',
            'List',
            'Check Item',
            'Standard',
            'Prod Status',
            'Quality Status',
            'Created At'
        ];
    }

    public function map($row): array
    {
        static $counter = 0;
        $counter++;

        return [
            $counter,
            \Carbon\Carbon::parse($row->date)->format('d/m/Y'),
            $row->shift,
            $row->area,
            $row->line,
            $row->model,
            $row->station,
            $row->list,
            $row->check_item,
            $row->standard,
            $row->prod_status,
            $row->quality_status,
            \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // All cells border
            'A1:M' . ($sheet->getHighestRow()) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 12,  // Tanggal
            'C' => 8,   // Shift
            'D' => 10,  // Area
            'E' => 8,   // Line
            'F' => 20,  // Model
            'G' => 15,  // Station
            'H' => 8,   // List
            'I' => 30,  // Check Item
            'J' => 20,  // Standard
            'K' => 12,  // Prod Status
            'L' => 15,  // Quality Status
            'M' => 18   // Created At
        ];
    }
}
