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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RenovationsDetailSheet implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle
{
    public function title(): string
    {
        return 'Detailed Renovations';
    }

    public function headings(): array
    {
        return [
            [
                "#\nرقم",
                "Renovation Name\nاسم المشروع",
                "Renovation Date\nتاريخ الترميم",
                "Governorate\nالمحافظة",
                "Region\nالمنطقة",
                "Linked Entity Type\nنوع الجهة",
                "Entity Identifier\nمعرّف الجهة",
                "Entity Name / Reference\nاسم الجهة / المرجع",
                "Site Code\nرمز الموقع",
                "Site Name\nاسم الموقع",
                "Cost (JOD)\nالتكلفة (دينار)",
                "Description\nالوصف"
            ]
        ];
    }

    public function array(): array
    {
        $renovations = DB::table('renovations')
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
                'renovations.id',
                'renovations.name',
                'renovations.date',
                'renovations.cost',
                'renovations.description',
                'renovations.innovatable_type',
                DB::raw('COALESCE(direct_sites.governorate, building_sites.governorate, land_sites.governorate) as governorate'),
                DB::raw('COALESCE(direct_sites.region, building_sites.region, land_sites.region) as region'),
                DB::raw('COALESCE(direct_sites.code, building_sites.code, land_sites.code) as site_code'),
                DB::raw('COALESCE(direct_sites.name, building_sites.name, land_sites.name) as site_name'),
                'buildings.code as building_code',
                'buildings.name as building_name',
                'lands.plot_number as land_plot_number',
                'lands.basin as land_basin',
                'lands.neighborhood as land_neighborhood',
                'lands.village as land_village'
            )
            ->orderByDesc('renovations.date')
            ->orderByDesc('renovations.id')
            ->get();

        $data = [];
        $rowNumber = 1;

        foreach ($renovations as $renovation) {
            $governorateName = $this->formatGovernorate($renovation->governorate);
            $regionName = $this->formatRegion($renovation->region);
            $renovationDate = $renovation->date ? Carbon::parse($renovation->date)->format('Y-m-d') : 'N/A';
            $cost = $renovation->cost !== null ? round((float) $renovation->cost, 2) : '';

            [$entityType, $entityIdentifier, $entityName] = $this->resolveEntityMetadata($renovation);

            $data[] = [
                $rowNumber++,
                $renovation->name,
                $renovationDate,
                $governorateName,
                $regionName,
                $entityType,
                $entityIdentifier,
                $entityName,
                $renovation->site_code ?? 'N/A',
                $renovation->site_name ?? 'N/A',
                $cost,
                $renovation->description ?? '',
            ];
        }

        return $data;
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
                    'startColor' => ['rgb' => '6A5ACD']
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

                $sheet->getRowDimension(1)->setRowHeight(38);

                $columnWidths = [
                    'A' => 6,
                    'B' => 26,
                    'C' => 14,
                    'D' => 18,
                    'E' => 14,
                    'F' => 18,
                    'G' => 18,
                    'H' => 28,
                    'I' => 14,
                    'J' => 22,
                    'K' => 16,
                    'L' => 40,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C2:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G2:J' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('K2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('F2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('L2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
        ];
    }

    private function resolveEntityMetadata($renovation): array
    {
        $typeClass = $renovation->innovatable_type;
        $type = $typeClass ? class_basename($typeClass) : 'Unknown';

        return match ($type) {
            'Building' => [
                'Building',
                $renovation->building_code ?? 'N/A',
                $renovation->building_name ?? 'N/A',
            ],
            'Site' => [
                'Site',
                $renovation->site_code ?? 'N/A',
                $renovation->site_name ?? 'N/A',
            ],
            'Land' => [
                'Land',
                $renovation->land_plot_number ? 'Plot ' . $renovation->land_plot_number : 'N/A',
                $this->formatLandName($renovation),
            ],
            default => [
                $type,
                'N/A',
                'N/A',
            ],
        };
    }

    private function formatLandName($renovation): string
    {
        $parts = array_filter([
            $renovation->land_plot_number ? 'Plot ' . $renovation->land_plot_number : null,
            $renovation->land_basin ? 'Basin ' . $renovation->land_basin : null,
            $renovation->land_neighborhood,
            $renovation->land_village,
        ]);

        return $parts ? implode(' - ', $parts) : 'N/A';
    }

    private function formatGovernorate($code): string
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

    private function formatRegion($region): string
    {
        return match ((int) $region) {
            1 => 'Capital',
            2 => 'North',
            3 => 'Middle',
            4 => 'South',
            default => 'Unknown',
        };
    }
}
