<?php

namespace App\Exports;

use App\Models\LogDetailCs;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Facades\Log;

class ChecksheetExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents, WithChunkReading
{
    protected $filters;
    protected $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        try {
            Log::info('Building Excel query with filters', $this->filters);
            
            return LogDetailCs::query()
                ->with(['log' => function($query) {
                    $query->select('id', 'date', 'shift', 'area', 'line', 'model');
                }])
                ->when($this->filters['area'] ?? null, function ($query, $area) {
                    $query->whereHas('log', function ($q) use ($area) {
                        $q->where('area', $area);
                    });
                })
                ->when($this->filters['line'] ?? null, function ($query, $line) {
                    $query->whereHas('log', function ($q) use ($line) {
                        $q->where('line', $line);
                    });
                })
                ->when($this->filters['model'] ?? null, function ($query, $model) {
                    $query->whereHas('log', function ($q) use ($model) {
                        $q->where('model', $model);
                    });
                })
                ->when($this->filters['date'] ?? null, function ($query, $date) {
                    $query->whereHas('log', function ($q) use ($date) {
                        $q->whereDate('date', $date);
                    });
                })
                ->when($this->filters['shift'] ?? null, function ($query, $shift) {
                    $query->whereHas('log', function ($q) use ($shift) {
                        $q->where('shift', $shift);
                    });
                })
                ->select('id', 'log_cs_id', 'station', 'list', 'check_item', 'standard', 'prod_status', 'quality_status', 'created_at', 'updated_at')
                ->orderBy('created_at', 'desc');
            
        } catch (\Exception $e) {
            Log::error('Error in Excel query', ['error' => $e->getMessage()]);
            throw $e;
        }
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
            'Created At',
            'Updated At'
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        
        try {
            return [
                $this->rowNumber,
                $row->log ? \Carbon\Carbon::parse($row->log->date)->format('d/m/Y') : '-',
                $row->log ? ($row->log->shift ?? '-') : '-',
                $row->log ? ($row->log->area ?? '-') : '-',
                $row->log ? ($row->log->line ?? '-') : '-',
                $row->log ? ($row->log->model ?? '-') : '-',
                $row->station ?? '-',
                $row->list ?? '-',
                $row->check_item ?? '-',
                $row->standard ?? '-',
                $row->prod_status ?? '-',
                $row->quality_status ?? 'Pending',
                $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i:s') : '-',
                $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y H:i:s') : '-',
            ];
        } catch (\Exception $e) {
            Log::error('Error mapping row', ['error' => $e->getMessage(), 'row_id' => $row->id ?? 'unknown']);
            return [
                $this->rowNumber, 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error', 'Error'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Checksheet Data';
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 records at a time
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set row height for header
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(25);
                
                // Apply borders to all data
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $highestColumn = $event->sheet->getDelegate()->getHighestColumn();
                
                $event->sheet->getDelegate()->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Wrap text for long content columns
                $event->sheet->getDelegate()->getStyle('I:J')->getAlignment()->setWrapText(true);
                
                // Center align specific columns
                $event->sheet->getDelegate()->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
                $event->sheet->getDelegate()->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date
                $event->sheet->getDelegate()->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Shift
                $event->sheet->getDelegate()->getStyle('K:L')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status columns
            },
        ];
    }
}
