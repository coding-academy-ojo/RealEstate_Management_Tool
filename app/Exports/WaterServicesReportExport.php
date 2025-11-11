<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WaterServicesReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new \App\Exports\Sheets\WaterServicesSummarySheet(),
            new \App\Exports\Sheets\WaterReadingsDetailSheet(),
        ];
    }
}
