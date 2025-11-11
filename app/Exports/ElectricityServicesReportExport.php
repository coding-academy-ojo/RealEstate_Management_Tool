<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ElectricityServicesReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new \App\Exports\Sheets\ElectricityServicesSummarySheet(),
            new \App\Exports\Sheets\ElectricityReadingsDetailSheet(),
        ];
    }
}
