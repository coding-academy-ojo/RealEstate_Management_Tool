<?php

namespace App\Exports\Templates;

use App\Models\WaterService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WaterReadingsBulkTemplate implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection(): Collection
    {
        $today = now()->format('Y-m-d');

        $services = WaterService::with(['building', 'waterCompany', 'latestReading'])
            ->where('is_active', true)
            ->orderBy('registration_number')
            ->get()
            ->map(function (WaterService $service) use ($today) {
                $previousReading = optional($service->latestReading)->current_reading;

                return [
                    $service->registration_number,
                    $service->iron_number,
                    $service->meter_owner_name,
                    optional($service->waterCompany)->name ?? $service->company_name,
                    optional($service->building)->name ?? 'Unassigned',
                    $previousReading !== null ? (float) $previousReading : 0,
                    $today,
                    '',
                    '',
                    'No',
                    '',
                ];
            });

        return $services;
    }

    public function headings(): array
    {
        return [
            'Registration #',
            'Iron #',
            'Meter Owner',
            'Water Company',
            'Building',
            'Previous Reading (m3)',
            'Reading Date (YYYY-MM-DD)',
            'Current Reading (m3)',
            'Bill Amount (JOD)',
            'Paid (Yes/No)',
            'Notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(16);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(32);
        $sheet->getColumnDimension('F')->setWidth(24);
        $sheet->getColumnDimension('G')->setWidth(24);
        $sheet->getColumnDimension('H')->setWidth(22);
        $sheet->getColumnDimension('I')->setWidth(22);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(32);

        return [];
    }
}
