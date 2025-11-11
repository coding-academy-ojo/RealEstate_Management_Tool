<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class WaterReadingsDetailSheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    protected array $unpaidRows = [];

    public function array(): array
    {
        $this->unpaidRows = [];

        $readings = DB::table('water_readings')
            ->join('water_services', 'water_readings.water_service_id', '=', 'water_services.id')
            ->join('buildings', 'water_services.building_id', '=', 'buildings.id')
            ->join('sites', 'buildings.site_id', '=', 'sites.id')
            ->leftJoin('water_companies', 'water_services.water_company_id', '=', 'water_companies.id')
            ->select(
                'water_readings.reading_date',
                'water_readings.current_reading',
                'water_readings.consumption_value',
                'water_readings.bill_amount',
                'water_readings.is_paid',
                'sites.governorate',
                'sites.region',
                'sites.code as site_code',
                'sites.name as site_name',
                'buildings.code as building_code',
                'buildings.name as building_name',
                'water_services.registration_number',
                DB::raw('COALESCE(water_companies.name, water_services.company_name) as company_name'),
                DB::raw('LAG(water_readings.current_reading) OVER (
                    PARTITION BY water_readings.water_service_id
                    ORDER BY COALESCE(water_readings.reading_date, water_readings.created_at), water_readings.id
                ) as previous_reading'),
                'water_readings.notes'
            )
            ->orderByDesc('water_readings.reading_date')
            ->orderByDesc('water_readings.id')
            ->get();

        $data = [];
        $rowNumber = 1;

        foreach ($readings as $reading) {
            $governorateName = $this->getGovernorateName($reading->governorate);
            $regionName = $this->getRegionName($reading->region);
            $readingDate = $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A';
            $currentReading = $reading->current_reading !== null ? round((float) $reading->current_reading, 2) : '';
            $previousReading = $reading->previous_reading !== null ? round((float) $reading->previous_reading, 2) : '';
            $consumption = $reading->consumption_value !== null ? round((float) $reading->consumption_value, 2) : '';
            $billAmount = $reading->bill_amount !== null ? round((float) $reading->bill_amount, 2) : '';
            $isPaid = (bool) $reading->is_paid;
            $statusLabel = $isPaid ? 'Paid' : 'Unpaid';
            $excelRow = $rowNumber + 1;

            $data[] = [
                $rowNumber,
                $readingDate,
                $governorateName,
                $regionName,
                $reading->site_code ?? 'N/A',
                $reading->site_name ?? 'N/A',
                $reading->building_code ?? 'N/A',
                $reading->building_name ?? 'N/A',
                $reading->registration_number ?? 'N/A',
                $reading->company_name ?? 'N/A',
                $currentReading,
                $previousReading,
                $consumption,
                $billAmount,
                $statusLabel,
                $reading->notes ?? ''
            ];

            if (!$isPaid) {
                $this->unpaidRows[] = $excelRow;
            }

            $rowNumber++;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            [
                "#\nرقم",
                "Date\nتاريخ",
                "Governorate\nالمحافظة",
                "Region\nالمنطقة",
                "Site Code\nرمز الموقع",
                "Site Name\nاسم الموقع",
                "Building Code\nرمز المبنى",
                "Building\nالمبنى",
                "Service #\nرقم الخدمة",
                "Company\nالشركة",
                "Current (m³)\nالقراءة الحالية",
                "Previous (m³)\nالقراءة السابقة",
                "Consumption (m³)\nالاستهلاك",
                "Bill (JOD)\nالفاتورة",
                "Status\nالحالة",
                "Notes\nملاحظات"
            ]
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getRowDimension(1)->setRowHeight(35);

                // Set column widths
                $columnWidths = [
                    'A' => 8,  'B' => 12, 'C' => 16, 'D' => 12,
                    'E' => 12, 'F' => 20, 'G' => 14, 'H' => 20,
                    'I' => 15, 'J' => 18, 'K' => 14, 'L' => 14,
                    'M' => 16, 'N' => 14, 'O' => 12, 'P' => 25
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Apply borders
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Center align
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K2:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Left align text columns
                $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('P2:P' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Highlight unpaid rows in soft red to match electricity report styling
                foreach ($this->unpaidRows as $row) {
                    $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8D7DA']
                        ],
                        'font' => [
                            'color' => ['rgb' => '842029']
                        ]
                    ]);
                }
            }
        ];
    }

    public function title(): string
    {
        return 'Detailed Readings';
    }

    private function getGovernorateName($code): string
    {
        return match ($code) {
            'AM' => 'Amman - عمّان',
            'IR' => 'Irbid - إربد',
            'AJ' => 'Ajloun - عجلون',
            'JA' => 'Jerash - جرش',
            'MA' => 'Mafraq - المفرق',
            'BA' => 'Balqa - البلقاء',
            'ZA' => 'Zarqa - الزرقاء',
            'KA' => 'Karak - الكرك',
            'TF' => 'Tafilah - الطفيلة',
            'MN' => 'Ma\'an - معان',
            'AQ' => 'Aqaba - العقبة',
            'MF' => 'Madaba - مادبا',
            default => $code ?? 'N/A'
        };
    }

    private function getRegionName($region): string
    {
        return match ($region) {
            1 => 'Capital',
            2 => 'North',
            3 => 'Middle',
            4 => 'South',
            default => 'N/A'
        };
    }
}
