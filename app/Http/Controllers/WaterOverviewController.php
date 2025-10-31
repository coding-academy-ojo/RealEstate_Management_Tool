<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\WaterCompany;
use App\Models\WaterReading;
use App\Models\WaterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WaterOverviewController extends Controller
{
  public function overview(): View
  {
    $totalServices = WaterService::count();
    $activeServices = WaterService::where('is_active', true)->count();
    $inactiveServices = WaterService::where('is_active', false)->count();
    $softDeletedServices = WaterService::onlyTrashed()->count();
    $totalCompanies = WaterCompany::withTrashed()->count();

    $periodStart = Carbon::now()->subMonths(11)->startOfMonth();
    $periodEnd = Carbon::now()->endOfMonth();

    $consumptionLast12Months = (float) WaterReading::whereBetween('reading_date', [$periodStart, $periodEnd])
      ->sum('consumption_value');
    $readingCountLast12 = (int) WaterReading::whereBetween('reading_date', [$periodStart, $periodEnd])->count();
    $averageConsumption = $readingCountLast12 > 0
      ? $consumptionLast12Months / $readingCountLast12
      : 0.0;

    $outstandingAmount = (float) WaterReading::where('is_paid', false)->sum('bill_amount');

    $driver = WaterReading::query()->getConnection()->getDriverName();
    $monthExpression = match ($driver) {
      'sqlite' => 'strftime("%Y-%m", reading_date)',
      'pgsql' => "to_char(reading_date, 'YYYY-MM')",
      default => "DATE_FORMAT(reading_date, '%Y-%m')",
    };

    $rawMonthly = WaterReading::selectRaw("{$monthExpression} as month, SUM(consumption_value) as total")
      ->whereBetween('reading_date', [$periodStart, $periodEnd])
      ->groupBy('month')
      ->orderBy('month')
      ->pluck('total', 'month');

    $monthSequence = collect(range(0, 11))->map(function (int $offset) use ($periodStart) {
      return $periodStart->copy()->addMonths($offset);
    });

    $consumptionTrend = [
      'labels' => $monthSequence->map(fn(Carbon $date) => $date->format('M Y'))->toArray(),
      'series' => $monthSequence
        ->map(fn(Carbon $date) => round((float) ($rawMonthly[$date->format('Y-m')] ?? 0), 2))
        ->toArray(),
    ];

    $companyBreakdownRaw = WaterCompany::withTrashed()
      ->withCount('services')
      ->orderByDesc('services_count')
      ->get();

    $topCompanies = $companyBreakdownRaw->take(5)
      ->map(fn(WaterCompany $company) => [
        'label' => $company->name,
        'count' => (int) $company->services_count,
      ]);

    $othersCount = max(0, (int) $companyBreakdownRaw->skip(5)->sum('services_count'));
    if ($othersCount > 0) {
      $topCompanies->push([
        'label' => 'Other Companies',
        'count' => $othersCount,
      ]);
    }

    $companyBreakdown = [
      'labels' => $topCompanies->pluck('label')->toArray(),
      'series' => $topCompanies->pluck('count')->toArray(),
    ];

    $governorateDistribution = WaterService::selectRaw('sites.governorate as code, COUNT(*) as total')
      ->join('buildings', 'water_services.building_id', '=', 'buildings.id')
      ->join('sites', 'buildings.site_id', '=', 'sites.id')
      ->groupBy('sites.governorate')
      ->orderByDesc('total')
      ->get()
      ->map(function ($row) {
        $site = new Site(['governorate' => $row->code]);

        return [
          'code' => $row->code,
          'label' => $site->governorate_name_en ?? $row->code,
          'total' => (int) $row->total,
        ];
      });

    $governorateBreakdown = [
      'labels' => $governorateDistribution->pluck('label')->toArray(),
      'series' => $governorateDistribution->pluck('total')->toArray(),
    ];

    $outstandingAggregates = WaterReading::select('water_service_id')
      ->selectRaw('SUM(COALESCE(bill_amount, 0)) as total_due')
      ->selectRaw('MAX(COALESCE(reading_date, created_at)) as last_reading_at')
      ->where('is_paid', false)
      ->groupBy('water_service_id')
      ->orderByDesc('total_due')
      ->take(5)
      ->get();

    $services = WaterService::with(['building.site', 'waterCompany'])
      ->whereIn('id', $outstandingAggregates->pluck('water_service_id'))
      ->get()
      ->keyBy('id');

    $outstandingServices = $outstandingAggregates
      ->map(function ($aggregate) use ($services) {
        $service = $services->get($aggregate->water_service_id);

        if (!$service) {
          return null;
        }

        return [
          'service' => $service,
          'total_due' => (float) $aggregate->total_due,
          'last_reading_at' => $aggregate->last_reading_at
            ? Carbon::parse($aggregate->last_reading_at)
            : null,
        ];
      })
      ->filter()
      ->values();

    $recentReadings = WaterReading::with(['waterService.building.site', 'waterService.waterCompany'])
      ->orderByDesc(DB::raw('COALESCE(reading_date, created_at)'))
      ->take(6)
      ->get();

    $summaryStats = [
      'total_services' => $totalServices,
      'active_services' => $activeServices,
      'inactive_services' => $inactiveServices,
      'soft_deleted_services' => $softDeletedServices,
      'total_companies' => $totalCompanies,
      'consumption_last_12_months' => round($consumptionLast12Months, 2),
      'average_consumption' => round($averageConsumption, 2),
      'outstanding_amount' => round($outstandingAmount, 2),
      'active_ratio' => $totalServices > 0 ? round(($activeServices / $totalServices) * 100, 1) : 0.0,
    ];

    return view('water.overview', [
      'summaryStats' => $summaryStats,
      'consumptionTrend' => $consumptionTrend,
      'companyBreakdown' => $companyBreakdown,
      'governorateBreakdown' => $governorateBreakdown,
      'outstandingServices' => $outstandingServices,
      'recentReadings' => $recentReadings,
    ]);
  }
}
