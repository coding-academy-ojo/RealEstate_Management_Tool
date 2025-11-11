<?php

namespace App\Exports;

use App\Exports\Sheets\RenovationsDetailSheet;
use App\Exports\Sheets\RenovationsSummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RenovationsReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new RenovationsSummarySheet(),
            new RenovationsDetailSheet(),
        ];
    }
}
