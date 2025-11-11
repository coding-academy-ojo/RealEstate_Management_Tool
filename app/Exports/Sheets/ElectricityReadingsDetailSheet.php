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

class ElectricityReadingsDetailSheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    protected array $unpaidRows = [];
    protected array $solarRows = [];

    public function array(): array
    {
        $this->unpaidRows = [];
        $this->solarRows = [];

        $readings = DB::table('electric_readings')
            ->join('electricity_services', 'electric_readings.electric_service_id', '=', 'electricity_services.id')
            ->join('buildings', 'electricity_services.building_id', '=', 'buildings.id')
            ->join('sites', 'buildings.site_id', '=', 'sites.id')
            ->leftJoin('electricity_companies', 'electricity_services.electricity_company_id', '=', 'electricity_companies.id')
            ->select(
                'electric_readings.reading_date',
                'electric_readings.imported_current',
                'electric_readings.imported_calculated',
                'electric_readings.produced_current',
                'electric_readings.produced_calculated',
                'electric_readings.saved_energy',
                'electric_readings.consumption_value',
                'electric_readings.bill_amount',
                'electric_readings.is_paid',
                'electricity_services.has_solar_power as has_solar',
                'sites.governorate',
                'sites.region',
                'sites.code as site_code',
                'sites.name as site_name',
                'buildings.code as building_code',
                'buildings.name as building_name',
                'electricity_services.registration_number',
                DB::raw('COALESCE(electricity_companies.name, electricity_services.company_name) as company_name'),
                'electric_readings.notes'
            )
            ->orderByDesc('electric_readings.reading_date')
            ->orderByDesc('electric_readings.id')
            ->get();

        $data = [];
        $rowNumber = 1;

        foreach ($readings as $reading) {
            $governorateName = $this->getGovernorateName($reading->governorate);
            $regionName = $this->getRegionName($reading->region);
            $readingDate = $reading->reading_date ? Carbon::parse($reading->reading_date)->format('Y-m-d') : 'N/A';
            $importedCurrent = $reading->imported_current !== null ? round((float) $reading->imported_current, 2) : '';
            $importedCalculated = $reading->imported_calculated !== null ? round((float) $reading->imported_calculated, 2) : '';
            $producedCurrent = $reading->produced_current !== null ? round((float) $reading->produced_current, 2) : '';
            $producedCalculated = $reading->produced_calculated !== null ? round((float) $reading->produced_calculated, 2) : '';
            $savedEnergy = $reading->saved_energy !== null ? round((float) $reading->saved_energy, 2) : '';
            $consumption = $reading->consumption_value !== null ? round((float) $reading->consumption_value, 2) : '';
            $billAmount = $reading->bill_amount !== null ? round((float) $reading->bill_amount, 2) : '';
            $isPaid = (bool) $reading->is_paid;
            $isSolar = (bool) $reading->has_solar;
            $serviceType = $isSolar ? 'Solar / Net Metering' : 'Standard Supply';
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
                $serviceType,
                $importedCurrent,
                $importedCalculated,
                $producedCurrent,
                $producedCalculated,
                $savedEnergy,
                $consumption,
                $billAmount,
                $statusLabel,
                $reading->notes ?? ''
            ];

            if (!$isPaid) {
                $this->unpaidRows[] = $excelRow;
            }

            if ($isSolar) {
                $this->solarRows[] = $excelRow;
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
                "Reading Date\nتاريخ القراءة",
                "Governorate\nالمحافظة",
                "Region\nالمنطقة",
                "Site Code\nرمز الموقع",
                "Site Name\nاسم الموقع",
                "Building Code\nرمز المبنى",
                "Building Name\nاسم المبنى",
                "Service Number\nرقم الخدمة",
                "Distribution Company\nشركة التوزيع",
                "Service Type\nنوع الخدمة",
                "Imported Reading - Current (kWh)\nالقراءة المستجرة الحالية",
                "Imported Reading - Calculated (kWh)\nالقراءة المستجرة المحتسبة",
                "Produced Reading - Current (kWh)\nالقراءة المصدّرة الحالية",
                "Produced Reading - Calculated (kWh)\nالقراءة المصدّرة المحتسبة",
                "Saved Energy (kWh)\nالطاقة الموفرة",
                "Net Consumption (kWh)\nالاستهلاك الصافي",
                "Bill Amount (JOD)\nقيمة الفاتورة",
                "Billing Status\nحالة الدفع",
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
                    'startColor' => ['rgb' => 'FFC000']
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
                    'A' => 8,  'B' => 14, 'C' => 16, 'D' => 12,
                    'E' => 12, 'F' => 22, 'G' => 14, 'H' => 20,
                    'I' => 15, 'J' => 20, 'K' => 18, 'L' => 16,
                    'M' => 18, 'N' => 18, 'O' => 18, 'P' => 16,
                    'Q' => 14, 'R' => 14, 'S' => 14, 'T' => 26
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
                $sheet->getStyle('I2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('L2:R' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('S2:S' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Left align text columns
                $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('J2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('T2:T' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Highlight unpaid rows in soft red for visibility
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

                // Highlight solar service cells in yellow
                foreach ($this->solarRows as $row) {
                    $sheet->getStyle('K' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFF3BF']
                        ],
                        'font' => [
                            'color' => ['rgb' => '000000']
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
