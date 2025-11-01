<?php

namespace App\Http\Controllers;

use App\Models\ElectricReading;
use App\Models\ElectricityCompany;
use App\Models\ElectricityService;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ElectricityOverviewController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:electricity');
  }

  public function index(): View
  {
    $totalServices = ElectricityService::count();
    $activeServices = ElectricityService::where('is_active', true)->count();
    $inactiveServices = ElectricityService::where('is_active', false)->count();
    $softDeletedServices = ElectricityService::onlyTrashed()->count();
    $totalCompanies = ElectricityCompany::withTrashed()->count();

    $periodStart = Carbon::now()->subMonths(11)->startOfMonth();
    $periodEnd = Carbon::now()->endOfMonth();

    $consumptionLast12Months = (float) ElectricReading::whereBetween('reading_date', [$periodStart, $periodEnd])
      ->sum('consumption_value');

    $readingCountLast12 = (int) ElectricReading::whereBetween('reading_date', [$periodStart, $periodEnd])->count();
    $averageConsumption = $readingCountLast12 > 0
      ? $consumptionLast12Months / $readingCountLast12
      : 0.0;

    $outstandingAmount = (float) ElectricReading::where('is_paid', false)->sum('bill_amount');

    $driver = ElectricReading::query()->getConnection()->getDriverName();
    $monthExpression = match ($driver) {
      'sqlite' => 'strftime("%Y-%m", reading_date)',
      'pgsql' => "to_char(reading_date, 'YYYY-MM')",
      default => "DATE_FORMAT(reading_date, '%Y-%m')",
    };

    $rawMonthly = ElectricReading::selectRaw("{$monthExpression} as month, SUM(consumption_value) as total")
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

    $companyBreakdownRaw = ElectricityCompany::withTrashed()
      ->withCount('services')
      ->orderByDesc('services_count')
      ->get();

    $topCompanies = $companyBreakdownRaw->take(5)
      ->map(fn(ElectricityCompany $company) => [
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

    $governorateDistribution = ElectricityService::selectRaw('sites.governorate as code, COUNT(*) as total')
      ->join('buildings', 'electricity_services.building_id', '=', 'buildings.id')
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

    $outstandingAggregates = ElectricReading::select('electric_service_id')
      ->selectRaw('SUM(COALESCE(bill_amount, 0)) as total_due')
      ->selectRaw('MAX(COALESCE(reading_date, created_at)) as last_reading_at')
      ->where('is_paid', false)
      ->groupBy('electric_service_id')
      ->orderByDesc('total_due')
      ->take(5)
      ->get();

    $services = ElectricityService::with(['building.site', 'electricityCompany'])
      ->whereIn('id', $outstandingAggregates->pluck('electric_service_id'))
      ->get()
      ->keyBy('id');

    $outstandingServices = $outstandingAggregates
      ->map(function ($aggregate) use ($services) {
        $service = $services->get($aggregate->electric_service_id);

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

    $recentReadings = ElectricReading::with([
      'electricityService.building.site',
      'electricityService.electricityCompany',
    ])
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

    return view('electricity.overview', [
      'summaryStats' => $summaryStats,
      'consumptionTrend' => $consumptionTrend,
      'companyBreakdown' => $companyBreakdown,
      'governorateBreakdown' => $governorateBreakdown,
      'outstandingServices' => $outstandingServices,
      'recentReadings' => $recentReadings,
    ]);
  }
}
