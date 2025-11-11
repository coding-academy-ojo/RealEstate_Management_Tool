<?php

namespace App\Exports;

use App\Models\Site;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SitesHierarchyExportMultiSheet implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        // First sheet: All Sites (all data combined)
        $sheets[] = new AllSitesSheet();

        // Second sheet: Summary by Governorate
        $sheets[] = new SummarySheet();

        // Individual governorate sheets
        $governorates = Site::select('governorate')
            ->distinct()
            ->whereNotNull('governorate')
            ->where('governorate', '!=', '')
            ->orderBy('governorate')
            ->pluck('governorate');

        foreach ($governorates as $governorate) {
            $sheets[] = new GovernorateSheet($governorate);
        }

        return $sheets;
    }
}
