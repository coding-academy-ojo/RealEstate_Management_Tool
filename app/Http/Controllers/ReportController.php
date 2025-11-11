<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElectricityService;
use App\Models\ElectricReading;
use App\Models\Land;
use App\Models\Renovation;
use App\Models\Site;
use App\Models\WaterService;
use App\Models\WaterReading;
use App\Exports\SitesHierarchyExport;
use App\Exports\SitesHierarchyExportMultiSheet;
use App\Exports\WaterServicesReportExport;
use App\Exports\ElectricityServicesReportExport;
use App\Exports\RenovationsReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        $stats = [
            'total_sites' => Site::count(),
            'total_buildings' => Building::count(),
            'total_lands' => Land::count(),
            'total_water_services' => WaterService::count(),
            'total_electricity_services' => ElectricityService::count(),
            'total_renovations' => Renovation::count(),
            'total_water_readings' => WaterReading::count(),
            'total_electric_readings' => ElectricReading::count(),
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Export sites data
     */
    public function exportSites(Request $request)
    {
        $format = $request->get('format', 'csv');

        $sites = Site::with(['buildings', 'lands'])->get();

        if ($format === 'csv') {
            return $this->exportSitesCSV($sites);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export buildings data
     */
    public function exportBuildings(Request $request)
    {
        $format = $request->get('format', 'csv');

        $buildings = Building::with('site')->get();

        if ($format === 'csv') {
            return $this->exportBuildingsCSV($buildings);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export lands data
     */
    public function exportLands(Request $request)
    {
        $format = $request->get('format', 'csv');

        $lands = Land::with('site')->get();

        if ($format === 'csv') {
            return $this->exportLandsCSV($lands);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export water services data
     */
    public function exportWaterServices(Request $request)
    {
        $format = $request->get('format', 'csv');

        $services = WaterService::with(['building.site', 'waterCompany', 'readings'])->get();

        if ($format === 'csv') {
            return $this->exportWaterServicesCSV($services);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export electricity services data
     */
    public function exportElectricityServices(Request $request)
    {
        $format = $request->get('format', 'csv');

        $services = ElectricityService::with(['building.site', 'electricityCompany', 'readings'])->get();

        if ($format === 'csv') {
            return $this->exportElectricityServicesCSV($services);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export renovations data
     */
    public function exportRenovations(Request $request)
    {
        $format = $request->get('format', 'csv');

        $renovations = Renovation::with('building.site')->get();

        if ($format === 'csv') {
            return $this->exportRenovationsCSV($renovations);
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    // ========== CSV Export Methods ==========

    private function exportSitesCSV($sites)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sites_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($sites) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Headers
            fputcsv($file, [
                'Code',
                'Name',
                'Governorate',
                'Cluster',
                'Total Buildings',
                'Total Lands',
                'Total Area (m²)',
                'Created At'
            ]);

            // Data
            foreach ($sites as $site) {
                fputcsv($file, [
                    $site->code,
                    $site->name,
                    $site->governorate_name_en,
                    $site->cluster,
                    $site->buildings->count(),
                    $site->lands->count(),
                    $site->total_area,
                    $site->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportBuildingsCSV($buildings)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="buildings_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($buildings) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Code',
                'Name',
                'Site',
                'Area (m²)',
                'Property Type',
                'Contract Value (JOD)',
                'Payment Frequency',
                'Annual Increase Rate (%)',
                'Building Permit',
                'Occupancy Permit',
                'Profession Permit',
                'Created At'
            ]);

            foreach ($buildings as $building) {
                fputcsv($file, [
                    $building->code,
                    $building->name,
                    $building->site->name ?? 'N/A',
                    $building->area_m2,
                    ucfirst($building->property_type),
                    $building->contract_value ?? 'N/A',
                    $building->contract_payment_frequency ? ucfirst(str_replace('-', ' ', $building->contract_payment_frequency)) : 'N/A',
                    $building->annual_increase_rate ?? 'N/A',
                    $building->has_building_permit ? 'Yes' : 'No',
                    $building->has_occupancy_permit ? 'Yes' : 'No',
                    $building->has_profession_permit ? 'Yes' : 'No',
                    $building->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportLandsCSV($lands)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="lands_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($lands) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Plot Key',
                'Site',
                'Directorate',
                'Village',
                'Basin',
                'Neighborhood',
                'Plot Number',
                'Area (m²)',
                'Created At'
            ]);

            foreach ($lands as $land) {
                fputcsv($file, [
                    $land->plot_key,
                    $land->site->name ?? 'N/A',
                    $land->directorate,
                    $land->village,
                    $land->basin,
                    $land->neighborhood,
                    $land->plot_number,
                    $land->area_m2,
                    $land->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportWaterServicesCSV($services)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="water_services_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($services) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Building',
                'Site',
                'Subscriber Name',
                'Water Company',
                'Meter Number',
                'Registration Number',
                'Status',
                'Total Readings',
                'Created At'
            ]);

            foreach ($services as $service) {
                fputcsv($file, [
                    $service->building->name ?? 'N/A',
                    $service->building->site->name ?? 'N/A',
                    $service->subscriber_name,
                    $service->waterCompany->name ?? 'N/A',
                    $service->meter_number,
                    $service->registration_number,
                    $service->is_active ? 'Active' : 'Inactive',
                    $service->readings->count(),
                    $service->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportElectricityServicesCSV($services)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="electricity_services_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($services) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Building',
                'Site',
                'Subscriber Name',
                'Electricity Company',
                'Meter Number',
                'Registration Number',
                'Service Type',
                'Status',
                'Total Readings',
                'Created At'
            ]);

            foreach ($services as $service) {
                fputcsv($file, [
                    $service->building->name ?? 'N/A',
                    $service->building->site->name ?? 'N/A',
                    $service->subscriber_name,
                    $service->electricityCompany->name ?? 'N/A',
                    $service->meter_number,
                    $service->registration_number,
                    ucfirst(str_replace('_', ' ', $service->service_type)),
                    $service->is_active ? 'Active' : 'Inactive',
                    $service->readings->count(),
                    $service->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportRenovationsCSV($renovations)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="renovations_export_' . date('Y-m-d_His') . '.csv"',
        ];

        $callback = function() use ($renovations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Building',
                'Site',
                'Type',
                'Description',
                'Cost (JOD)',
                'Start Date',
                'End Date',
                'Status',
                'Created At'
            ]);

            foreach ($renovations as $renovation) {
                fputcsv($file, [
                    $renovation->building->name ?? 'N/A',
                    $renovation->building->site->name ?? 'N/A',
                    $renovation->type,
                    $renovation->description,
                    $renovation->cost,
                    $renovation->start_date?->format('Y-m-d') ?? 'N/A',
                    $renovation->end_date?->format('Y-m-d') ?? 'N/A',
                    ucfirst($renovation->status),
                    $renovation->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export full hierarchy: Sites -> Lands -> Buildings
     */
    public function exportAllHierarchy(Request $request)
    {
        $format = $request->get('format', 'xlsx');

        if ($format === 'xlsx') {
            return Excel::download(
                new SitesHierarchyExportMultiSheet(),
                'Orange_real_estate_data_' . date('Y-m-d_His') . '.xlsx'
            );
        }

        return redirect()->back()->with('error', 'Format not supported yet');
    }

    /**
     * Export Water Services Report
     */
    public function exportWaterServicesReport(Request $request)
    {
        return Excel::download(
            new WaterServicesReportExport(),
            'Orange_water_services_report_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Export Electricity Services Report
     */
    public function exportElectricityServicesReport(Request $request)
    {
        return Excel::download(
            new ElectricityServicesReportExport(),
            'Orange_electricity_services_report_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Export Renovations Report
     */
    public function exportRenovationsReport(Request $request)
    {
        return Excel::download(
            new RenovationsReportExport(),
            'Orange_renovations_report_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
