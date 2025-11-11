<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WaterServicesSummarySheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return [
            "Governorate\nالمحافظة",
            "Total Services\nإجمالي الخدمات",
            "Active\nنشط",
            "Inactive\nغير نشط",
            "Total Buildings\nإجمالي المباني",
            "Total Consumption (m³)\nإجمالي الاستهلاك",
            "Total Bills (JOD)\nإجمالي الفواتير",
            "Paid (JOD)\nمدفوع",
            "Unpaid (JOD)\nغير مدفوع",
        ];
    }

    public function array(): array
    {
        $data = [];

        // Get comprehensive stats by governorate
        $stats = DB::table('water_services')
            ->join('buildings', 'water_services.building_id', '=', 'buildings.id')
            ->join('sites', 'buildings.site_id', '=', 'sites.id')
            ->leftJoin('water_readings', 'water_services.id', '=', 'water_readings.water_service_id')
            ->select(
                'sites.governorate',
                DB::raw('COUNT(DISTINCT water_services.id) as total_services'),
                DB::raw('SUM(CASE WHEN water_services.is_active = 1 THEN 1 ELSE 0 END) as active_services'),
                DB::raw('SUM(CASE WHEN water_services.is_active = 0 THEN 1 ELSE 0 END) as inactive_services'),
                DB::raw('COUNT(DISTINCT buildings.id) as total_buildings'),
                DB::raw('SUM(COALESCE(water_readings.consumption_value, 0)) as total_consumption'),
                DB::raw('SUM(COALESCE(water_readings.bill_amount, 0)) as total_bills'),
                DB::raw('SUM(CASE WHEN water_readings.is_paid = 1 THEN COALESCE(water_readings.bill_amount, 0) ELSE 0 END) as paid_amount'),
                DB::raw('SUM(CASE WHEN water_readings.is_paid = 0 THEN COALESCE(water_readings.bill_amount, 0) ELSE 0 END) as unpaid_amount')
            )
            ->groupBy('sites.governorate')
            ->orderBy('sites.governorate')
            ->get();

        $totalServices = 0;
        $totalActive = 0;
        $totalInactive = 0;
        $totalBuildings = 0;
        $totalConsumption = 0;
        $totalBills = 0;
        $totalPaid = 0;
        $totalUnpaid = 0;

        foreach ($stats as $stat) {
            $governorateName = $this->getGovernorateName($stat->governorate);

            $data[] = [
                $governorateName,
                $stat->total_services,
                $stat->active_services,
                $stat->inactive_services,
                $stat->total_buildings,
                round($stat->total_consumption, 2),
                round($stat->total_bills, 2),
                round($stat->paid_amount, 2),
                round($stat->unpaid_amount, 2),
            ];

            $totalServices += $stat->total_services;
            $totalActive += $stat->active_services;
            $totalInactive += $stat->inactive_services;
            $totalBuildings += $stat->total_buildings;
            $totalConsumption += $stat->total_consumption;
            $totalBills += $stat->total_bills;
            $totalPaid += $stat->paid_amount;
            $totalUnpaid += $stat->unpaid_amount;
        }

        // Add total row
        $data[] = [
            'TOTAL - المجموع',
            $totalServices,
            $totalActive,
            $totalInactive,
            $totalBuildings,
            round($totalConsumption, 2),
            round($totalBills, 2),
            round($totalPaid, 2),
            round($totalUnpaid, 2),
        ];

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getRowDimension(1)->setRowHeight(35);
                $highestRow = $sheet->getHighestRow();

                // Style the total row
                $sheet->getStyle('A' . $highestRow . ':I' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFD966']
                    ],
                ]);

                // Center align all cells
                $sheet->getStyle('A1:I' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Left align governorate names
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Apply borders
                $sheet->getStyle('A1:I' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(16);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
            },
        ];
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
}
