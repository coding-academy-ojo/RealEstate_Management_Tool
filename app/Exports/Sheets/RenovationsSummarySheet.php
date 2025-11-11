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

class RenovationsSummarySheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Summary by Governorate';
    }

    public function headings(): array
    {
        return [
            "Governorate\nالمحافظة",
            "Total Renovations\nإجمالي الترميمات",
            "Average Cost (JOD)\nمتوسط التكلفة",
            "Highest Cost (JOD)\nأعلى تكلفة",
            "Total Cost (JOD)\nالتكلفة الإجمالية",
        ];
    }

    public function array(): array
    {
        $data = [];

        $stats = DB::table('renovations')
            ->leftJoin('buildings', function ($join) {
                $join->on('renovations.innovatable_id', '=', 'buildings.id')
                    ->where('renovations.innovatable_type', 'App\\Models\\Building');
            })
            ->leftJoin('sites as building_sites', 'buildings.site_id', '=', 'building_sites.id')
            ->leftJoin('lands', function ($join) {
                $join->on('renovations.innovatable_id', '=', 'lands.id')
                    ->where('renovations.innovatable_type', 'App\\Models\\Land');
            })
            ->leftJoin('sites as land_sites', 'lands.site_id', '=', 'land_sites.id')
            ->leftJoin('sites as direct_sites', function ($join) {
                $join->on('renovations.innovatable_id', '=', 'direct_sites.id')
                    ->where('renovations.innovatable_type', 'App\\Models\\Site');
            })
            ->select(
                DB::raw('COALESCE(direct_sites.governorate, building_sites.governorate, land_sites.governorate) as governorate'),
                DB::raw('COUNT(renovations.id) as total_renovations'),
                DB::raw('AVG(renovations.cost) as average_cost'),
                DB::raw('MAX(renovations.cost) as max_cost'),
                DB::raw('SUM(renovations.cost) as total_cost')
            )
            ->groupBy('governorate')
            ->orderBy('governorate')
            ->get();

        $totalRenovations = 0;
        $totalCost = 0;
        $overallMaxCost = 0;

        foreach ($stats as $stat) {
            $governorateCode = $stat->governorate;
            $governorateName = $this->formatGovernorate($governorateCode);
            $averageCost = round($stat->average_cost ?? 0, 2);
            $maxCost = round($stat->max_cost ?? 0, 2);
            $totalCostGovernorate = round($stat->total_cost ?? 0, 2);

            $data[] = [
                $governorateName,
                $stat->total_renovations,
                $averageCost,
                $maxCost,
                $totalCostGovernorate,
            ];

            $totalRenovations += $stat->total_renovations;
            $totalCost += $stat->total_cost ?? 0;
            $overallMaxCost = max($overallMaxCost, $stat->max_cost ?? 0);
        }

        $data[] = [
            'TOTAL - المجموع',
            $totalRenovations,
            $totalRenovations > 0 ? round($totalCost / $totalRenovations, 2) : 0,
            round($overallMaxCost, 2),
            round($totalCost, 2),
        ];

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E74C3C']
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
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->getRowDimension(1)->setRowHeight(35);
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle('A' . $highestRow . ':E' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFD966']
                    ],
                ]);

                $sheet->getStyle('A1:E' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle('A1:E' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                $sheet->getColumnDimension('A')->setWidth(30);
            },
        ];
    }

    private function formatGovernorate(?string $code): string
    {
        return match ($code) {
            'AM' => 'Amman - عمّان',
            'IR' => 'Irbid - إربد',
            'AJ' => 'Ajloun - عجلون',
            'JA' => 'Jerash - جرش',
            'MA' => 'Madaba - مادبا',
            'BA' => 'Balqa - البلقاء',
            'ZA' => 'Zarqa - الزرقاء',
            'KA' => 'Karak - الكرك',
            'TF' => 'Tafileh - الطفيلة',
            'MN' => 'Ma\'an - معان',
            'AQ' => 'Aqaba - العقبة',
            'MF' => 'Mafraq - المفرق',
            null => 'Unknown',
            default => $code,
        };
    }
}
