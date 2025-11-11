<?php

namespace App\Exports;

use App\Models\Site;
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

class SummarySheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Summary';
    }

    public function headings(): array
    {
        return [
            "Governorate\nالمحافظة",
            "No. of Sites\nعدد المواقع",
            "Total Sites Area (m²)\nمساحة المواقع الإجمالية",
            "No. of Lands\nعدد الأراضي",
            "No. of Buildings\nعدد المباني",
            "Total Buildings Area (m²)\nمساحة المباني الإجمالية",
        ];
    }

    public function array(): array
    {
        $data = [];

        // Get governorate statistics
        $governorates = Site::select('governorate')
            ->distinct()
            ->whereNotNull('governorate')
            ->where('governorate', '!=', '')
            ->orderBy('governorate')
            ->get();

        $totalSites = 0;
        $totalSitesArea = 0;
        $totalLands = 0;
        $totalBuildings = 0;
        $totalBuildingsArea = 0;

        foreach ($governorates as $gov) {
            $governorate = $gov->governorate;

            // Get governorate name
            $governorateName = match ($governorate) {
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
                default => $governorate
            };

            // Count sites and sum area
            $sitesData = Site::where('governorate', $governorate)
                ->selectRaw('COUNT(*) as count, SUM(area_m2) as total_area')
                ->first();

            $sitesCount = $sitesData->count ?? 0;
            $sitesArea = $sitesData->total_area ?? 0;

            // Count lands
            $landsCount = DB::table('lands')
                ->join('sites', 'lands.site_id', '=', 'sites.id')
                ->where('sites.governorate', $governorate)
                ->count();

            // Count buildings and sum area
            $buildingsData = DB::table('buildings')
                ->join('sites', 'buildings.site_id', '=', 'sites.id')
                ->where('sites.governorate', $governorate)
                ->selectRaw('COUNT(*) as count, SUM(buildings.area_m2) as total_area')
                ->first();

            $buildingsCount = $buildingsData->count ?? 0;
            $buildingsArea = $buildingsData->total_area ?? 0;

            $data[] = [
                $governorateName,
                $sitesCount,
                round($sitesArea, 2),
                $landsCount,
                $buildingsCount,
                round($buildingsArea, 2),
            ];

            // Add to totals
            $totalSites += $sitesCount;
            $totalSitesArea += $sitesArea;
            $totalLands += $landsCount;
            $totalBuildings += $buildingsCount;
            $totalBuildingsArea += $buildingsArea;
        }

        // Add total row
        $data[] = [
            'TOTAL - المجموع',
            $totalSites,
            round($totalSitesArea, 2),
            $totalLands,
            $totalBuildings,
            round($totalBuildingsArea, 2),
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

                // Set header row height and enable text wrapping
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getStyle('A1:F1')->getAlignment()->setWrapText(true);

                $highestRow = $sheet->getHighestRow();

                // Style the total row (last row)
                $sheet->getStyle('A' . $highestRow . ':F' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFD966']
                    ],
                ]);

                // Center align all cells
                $sheet->getStyle('A1:F' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Left align governorate names
                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Apply borders
                $sheet->getStyle('A1:F' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Auto-size columns
                foreach (range('A', 'F') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Set minimum widths
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(30);
            },
        ];
    }
}
