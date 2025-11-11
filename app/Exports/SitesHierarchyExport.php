<?php

namespace App\Exports;

use App\Models\Site;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SitesHierarchyExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $mergeRanges = [];
    protected $currentRow = 2; // Start after header
    protected $rowNumber = 1; // For numbering
    protected $solarServiceRows = []; // Track rows with solar services

    public function headings(): array
    {
        return [
            "#\nرقم",
            "Site Code\nكود الموقع",
            "Region\nالمنطقة",
            "Site Name\nاسم الموقع",
            "Site Area (m²)\nمساحة الموقع",
            "Land Plot\nمفتاح القطعة",
            "Directorate\nالمديرية",
            "Village\nالقرية",
            "Basin\nالحوض",
            "Neighborhood\nالحي",
            "Plot Number\nرقم القطعة",
            "Zoning\nالتنظيم",
            "Land Area (m²)\nمساحة القطعة",
            "Ownership Doc\nسند الملكية",
            "Site Plan\nمخطط الموقع",
            "Zoning Plan\nمخطط تنظيمي",
            "Map Location\nالموقع",
            "Building Code\nكود المبنى",
            "Building Name\nاسم المبنى",
            "Building Area (m²)\nمساحة المبنى",
            "Property Type\nنوع العقار",
            "Contract Value\nقيمة العقد",
            "Payment Frequency\nدورية الدفع",
            "Annual Increase %\nنسبة الزيادة السنوية",
            "Building Permit\nرخصة بناء",
            "Occupancy Permit\nرخصة إشغال",
            "Profession Permit\nرخصة مهن",
            "Water Services\nخدمات المياه",
            "Electricity Services\nخدمات الكهرباء",
        ];
    }

    public function array(): array
    {
        $data = [];
        $this->mergeRanges = [];
        $this->currentRow = 2;
        $this->rowNumber = 1; // Site counter

        Site::with(['lands.zoningStatuses', 'buildings.lands', 'buildings.waterServices.waterCompany', 'buildings.electricityServices.electricityCompany'])
            ->orderBy('code')
            ->chunk(100, function ($sites) use (&$data) {
                foreach ($sites as $site) {
                    $siteStartRow = $this->currentRow;
                    $siteNumber = $this->rowNumber; // Same number for all rows of this site
                    $siteCode = $site->code;
                    $siteRegion = match($site->region) {
                        1 => 'Capital',
                        2 => 'North',
                        3 => 'Middle',
                        4 => 'South',
                        default => $site->region ?? ''
                    };
                    $siteName = $site->name;
                    $siteArea = $site->area_m2;

                    // Group buildings by land
                    $buildingsByLand = [];
                    $unassignedBuildings = [];

                    foreach ($site->buildings as $building) {
                        $assigned = false;
                        if ($building->lands && $building->lands->count() > 0) {
                            foreach ($building->lands as $land) {
                                if (!isset($buildingsByLand[$land->id])) {
                                    $buildingsByLand[$land->id] = [];
                                }
                                $buildingsByLand[$land->id][] = $building;
                                $assigned = true;
                            }
                        }
                        if (!$assigned) {
                            $unassignedBuildings[] = $building;
                        }
                    }

                    // Process lands
                    if ($site->lands->count() > 0) {
                        foreach ($site->lands as $land) {
                            $landStartRow = $this->currentRow;

                            // Get zoning status - prefer Arabic name
                            $zoningStatusRecord = $land->zoningStatuses->first();
                            $zoningStatus = $zoningStatusRecord?->name_ar ?? $zoningStatusRecord?->name_en ?? '';

                            // Prepare land data with combined fields
                            $landPlot = $land->plot_key ?? '';

                            // Directorate (المديرية) - combine name and number
                            $directorate = $land->directorate ?? '';
                            if ($directorate && $land->directorate_number) {
                                $directorate = "{$land->directorate} ({$land->directorate_number})";
                            }

                            // Village (القرية) - combine name and number
                            $village = $land->village ?? '';
                            if ($village && $land->village_number) {
                                $village = "{$land->village} ({$land->village_number})";
                            }

                            // Basin (الحوض) - combine name and number
                            $basin = $land->basin ?? '';
                            if ($basin && $land->basin_number) {
                                $basin = "{$land->basin} ({$land->basin_number})";
                            }

                            // Neighborhood (الحي) - combine name and number
                            $neighborhood = $land->neighborhood ?? '';
                            if ($neighborhood && $land->neighborhood_number) {
                                $neighborhood = "{$land->neighborhood} ({$land->neighborhood_number})";
                            }

                            $plotNumber = $land->plot_number ?? '';
                            $landArea = $land->area_m2;

                            // Documents - Yes/No
                            $ownershipDoc = $land->ownership_doc ? 'Yes' : 'No';
                            $sitePlan = $land->site_plan ? 'Yes' : 'No';
                            $zoningPlan = $land->zoning_plan ? 'Yes' : 'No';

                            // Map location - use hyperlink formula if exists
                            $mapLocation = $land->map_location ?? '';

                            $assignedBuildings = $buildingsByLand[$land->id] ?? [];

                            if (count($assignedBuildings) > 0) {
                                foreach ($assignedBuildings as $building) {
                                    // Prepare Water Services info - ACTIVE ONLY
                                    $waterServices = [];
                                    foreach ($building->waterServices->where('is_active', true) as $ws) {
                                        $company = $ws->waterCompany?->name_ar ?? $ws->company_name_ar ?? '';
                                        $subscriber = $ws->meter_owner_name ?? '';
                                        $waterServices[] = "Company: {$company}, Subscriber: {$subscriber}, Reg#: {$ws->registration_number}, Iron#: {$ws->iron_number}";
                                    }
                                    $waterServicesText = !empty($waterServices) ? implode("\n", $waterServices) : 'None';

                                    // Prepare Electricity Services info - ACTIVE ONLY
                                    $electricServices = [];
                                    $hasSolar = false;
                                    foreach ($building->electricityServices->where('is_active', true) as $es) {
                                        $company = $es->electricityCompany?->name_ar ?? $es->company_name_ar ?? '';
                                        $solar = $es->has_solar_power ? '(Solar)' : '';
                                        if ($es->has_solar_power) {
                                            $hasSolar = true;
                                        }
                                        $electricServices[] = "Company: {$company}, Subscriber: {$es->subscriber_name}, Meter#: {$es->meter_number} {$solar}";
                                    }
                                    $electricServicesText = !empty($electricServices) ? implode("\n", $electricServices) : 'None';

                                    // Track if this row has solar
                                    if ($hasSolar) {
                                        $this->solarServiceRows[] = $this->currentRow;
                                    }

                                    $data[] = [
                                        $siteNumber,
                                        $siteCode,
                                        $siteRegion,
                                        $siteName,
                                        $siteArea,
                                        $landPlot,
                                        $directorate,
                                        $village,
                                        $basin,
                                        $neighborhood,
                                        $plotNumber,
                                        $zoningStatus,
                                        $landArea,
                                        $ownershipDoc,
                                        $sitePlan,
                                        $zoningPlan,
                                        $mapLocation,
                                        $building->code,
                                        $building->name,
                                        $building->area_m2,
                                        ucfirst($building->property_type),
                                        $building->contract_value ?? '',
                                        $building->contract_payment_frequency ? ucfirst(str_replace('-', ' ', $building->contract_payment_frequency)) : '',
                                        $building->annual_increase_rate ?? '',
                                        $building->has_building_permit ? 'Yes' : 'No',
                                        $building->has_occupancy_permit ? 'Yes' : 'No',
                                        $building->has_profession_permit ? 'Yes' : 'No',
                                        $waterServicesText,
                                        $electricServicesText,
                                    ];
                                    $this->currentRow++;
                                }

                                // Merge land cells if multiple buildings
                                if (count($assignedBuildings) > 1) {
                                    $landEndRow = $this->currentRow - 1;
                                    for ($col = 6; $col <= 17; $col++) { // Land columns (F to Q) - shifted by 1 for region
                                        $this->mergeRanges[] = $this->getColumnLetter($col) . $landStartRow . ':' . $this->getColumnLetter($col) . $landEndRow;
                                    }
                                }
                            } else {
                                // Land without buildings
                                $data[] = [
                                    $siteNumber,
                                    $siteCode,
                                    $siteRegion,
                                    $siteName,
                                    $siteArea,
                                    $landPlot,
                                    $directorate,
                                    $village,
                                    $basin,
                                    $neighborhood,
                                    $plotNumber,
                                    $zoningStatus,
                                    $landArea,
                                    $ownershipDoc,
                                    $sitePlan,
                                    $zoningPlan,
                                    $mapLocation,
                                    '', '', '', '', '', '', '', '', '', '', '', '',
                                ];
                                $this->currentRow++;
                            }
                        }
                    }

                    // Process unassigned buildings
                    foreach ($unassignedBuildings as $building) {
                        // Prepare Water Services info - ACTIVE ONLY
                        $waterServices = [];
                        foreach ($building->waterServices->where('is_active', true) as $ws) {
                            $company = $ws->waterCompany?->name_ar ?? $ws->company_name_ar ?? '';
                            $subscriber = $ws->meter_owner_name ?? '';
                            $waterServices[] = "Company: {$company}, Subscriber: {$subscriber}, Reg#: {$ws->registration_number}, Iron#: {$ws->iron_number}";
                        }
                        $waterServicesText = !empty($waterServices) ? implode("\n", $waterServices) : 'None';

                        // Prepare Electricity Services info - ACTIVE ONLY
                        $electricServices = [];
                        $hasSolar = false;
                        foreach ($building->electricityServices->where('is_active', true) as $es) {
                            $company = $es->electricityCompany?->name_ar ?? $es->company_name_ar ?? '';
                            $solar = $es->has_solar_power ? '(Solar)' : '';
                            if ($es->has_solar_power) {
                                $hasSolar = true;
                            }
                            $electricServices[] = "Company: {$company}, Subscriber: {$es->subscriber_name}, Meter#: {$es->meter_number} {$solar}";
                        }
                        $electricServicesText = !empty($electricServices) ? implode("\n", $electricServices) : 'None';

                        // Track if this row has solar
                        if ($hasSolar) {
                            $this->solarServiceRows[] = $this->currentRow;
                        }

                        $data[] = [
                            $siteNumber,
                            $siteCode,
                            $siteRegion,
                            $siteName,
                            $siteArea,
                            '', '', '', '', '', '', '', '', '', '', '', '', // Empty land columns (12 columns)
                            $building->code,
                            $building->name,
                            $building->area_m2,
                            ucfirst($building->property_type),
                            $building->contract_value ?? '',
                            $building->contract_payment_frequency ? ucfirst(str_replace('-', ' ', $building->contract_payment_frequency)) : '',
                            $building->annual_increase_rate ?? '',
                            $building->has_building_permit ? 'Yes' : 'No',
                            $building->has_occupancy_permit ? 'Yes' : 'No',
                            $building->has_profession_permit ? 'Yes' : 'No',
                            $waterServicesText,
                            $electricServicesText,
                        ];
                        $this->currentRow++;
                    }

                    // If site has neither lands nor buildings
                    if ($site->lands->count() === 0 && $site->buildings->count() === 0) {
                        $data[] = [
                            $siteNumber,
                            $siteCode,
                            $siteRegion,
                            $siteName,
                            $siteArea,
                            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                        ];
                        $this->currentRow++;
                    }

                    // Merge site columns for this site (including number column and region)
                    $siteEndRow = $this->currentRow - 1;
                    if ($siteEndRow > $siteStartRow) {
                        for ($col = 1; $col <= 5; $col++) { // Site columns including number and region (A to E)
                            $this->mergeRanges[] = $this->getColumnLetter($col) . $siteStartRow . ':' . $this->getColumnLetter($col) . $siteEndRow;
                        }
                    }

                    // Increment site number for next site
                    $this->rowNumber++;
                }
            });

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
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
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getAlignment()->setWrapText(true);

                // Apply different colors to header sections
                // Number column (A): Gray
                $sheet->getStyle('A1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '7F7F7F']
                    ],
                ]);

                // Site columns (B-E): Orange - includes Region
                $sheet->getStyle('B1:E1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FF7900']
                    ],
                ]);

                // Land columns (F-Q): Blue - includes new document and location fields
                $sheet->getStyle('F1:Q1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4']
                    ],
                ]);

                // Building columns (R-AA): Green - basic building info
                $sheet->getStyle('R1:AA1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '70AD47']
                    ],
                ]);

                // Water Services column (AB): Light Blue
                $sheet->getStyle('AB1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '9DC3E6']
                    ],
                ]);

                // Electricity Services column (AC): Light Yellow
                $sheet->getStyle('AC1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE699']
                    ],
                ]);

                // Apply merges
                foreach ($this->mergeRanges as $range) {
                    $sheet->mergeCells($range);
                }

                // Style all cells
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Apply borders
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Center align all cells horizontally and vertically
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Make Site Code (B), Land Plot (E), and Building Code (Q) columns bold with background colors
                // Site Code (B) - Light Orange background
                $sheet->getStyle('B2:B' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6CC']
                    ],
                ]);

                // Land Plot (F) - Light Blue background - shifted by 1
                $sheet->getStyle('F2:F' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9E9F7']
                    ],
                ]);

                // Building Code (R) - Light Green background - shifted by 1
                $sheet->getStyle('R2:R' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFD9']
                    ],
                ]);

                // Convert map locations (column Q) to hyperlinks - shifted by 1
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = $sheet->getCell('Q' . $row);
                    $url = $cell->getValue();
                    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                        $cell->getHyperlink()->setUrl($url);
                        $cell->setValue('View Map');
                        $sheet->getStyle('Q' . $row)->getFont()->getColor()->setRGB('0563C1');
                        $sheet->getStyle('Q' . $row)->getFont()->setUnderline(true);
                    }
                }

                // Apply yellow background to electricity column for rows with solar services
                foreach ($this->solarServiceRows as $row) {
                    $sheet->getStyle('AC' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFFF00']
                        ],
                    ]);
                }

                // Enable text wrapping for services columns - shifted by 1
                $sheet->getStyle('AB2:AC' . $highestRow)->getAlignment()->setWrapText(true);

                // Auto-size columns - loop through column numbers instead of letters
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
                }

                // Set minimum width for better readability
                $sheet->getColumnDimension('A')->setWidth(8);  // Row Number
                $sheet->getColumnDimension('B')->setWidth(15); // Site Code
                $sheet->getColumnDimension('C')->setWidth(15); // Region
                $sheet->getColumnDimension('D')->setWidth(30); // Site Name
                $sheet->getColumnDimension('F')->setWidth(20); // Land Plot Key
                $sheet->getColumnDimension('Q')->setWidth(15); // Map Location
                $sheet->getColumnDimension('R')->setWidth(15); // Building Code
                $sheet->getColumnDimension('S')->setWidth(30); // Building Name
                $sheet->getColumnDimension('AB')->setWidth(40); // Water Services
                $sheet->getColumnDimension('AC')->setWidth(40); // Electricity Services
            },
        ];
    }

    protected function getColumnLetter($columnNumber)
    {
        $dividend = $columnNumber;
        $columnName = '';
        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = (int)(($dividend - $modulo) / 26);
        }
        return $columnName;
    }
}
