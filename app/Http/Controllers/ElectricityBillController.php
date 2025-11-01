<?php

namespace App\Http\Controllers;

use App\Models\ElectricReading;
use App\Models\ElectricityCompany;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ElectricityBillController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:electricity');
  }

  public function index(Request $request): View
  {
    $filters = [
      'status' => $request->input('status', 'all'),
      'search' => trim((string) $request->input('search')),
      'company_id' => $request->input('company'),
      'solar' => $request->input('solar', 'all'),
      'governorate' => $request->input('governorate'),
      'date_from' => $request->input('date_from'),
      'date_to' => $request->input('date_to'),
      'amount_min' => $request->input('amount_min'),
      'amount_max' => $request->input('amount_max'),
    ];

    $sort = $request->input('sort', 'date');
    $direction = $request->input('direction', 'desc');

    $allReadings = ElectricReading::select([
      'id',
      'electric_service_id',
      'imported_calculated',
      'produced_calculated',
      'consumption_value',
      'reading_date',
      'created_at',
    ])
      ->orderBy('electric_service_id')
      ->orderBy('reading_date')
      ->orderBy('id')
      ->get()
      ->groupBy('electric_service_id');

    $computed = [];

    foreach ($allReadings as $serviceReadings) {
      $previousImported = 0.0;
      $previousProduced = 0.0;

      foreach ($serviceReadings as $index => $reading) {
        $consumption = round((float) ($reading->consumption_value ?? 0), 2);

        $computed[$reading->id] = [
          'previous_imported' => round($previousImported, 2),
          'previous_produced' => round($previousProduced, 2),
          'consumption' => $consumption,
        ];

        $previousImported = (float) ($reading->imported_calculated ?? 0);
        $previousProduced = (float) ($reading->produced_calculated ?? 0);
      }
    }

    $readingsQuery = ElectricReading::with([
      'electricityService.building.site',
      'electricityService.electricityCompany',
    ]);

    if ($filters['status'] === 'paid') {
      $readingsQuery->where('is_paid', true);
    } elseif ($filters['status'] === 'unpaid') {
      $readingsQuery->where('is_paid', false);
    }

    if ($filters['search']) {
      $searchTerm = '%' . $filters['search'] . '%';
      $parsedDate = null;

      try {
        $parsedDate = Carbon::parse($filters['search']);
      } catch (\Throwable $exception) {
        $parsedDate = null;
      }

      $readingsQuery->where(function ($query) use ($searchTerm, $parsedDate) {
        $query->whereHas('electricityService', function ($serviceQuery) use ($searchTerm) {
          $serviceQuery->where('registration_number', 'like', $searchTerm)
            ->orWhere('meter_number', 'like', $searchTerm)
            ->orWhere('company_name', 'like', $searchTerm)
            ->orWhere('company_name_ar', 'like', $searchTerm)
            ->orWhere('subscriber_name', 'like', $searchTerm)
            ->orWhereHas('building', function ($buildingQuery) use ($searchTerm) {
              $buildingQuery->where('name', 'like', $searchTerm)
                ->orWhereHas('site', function ($siteQuery) use ($searchTerm) {
                  $siteQuery->where('name', 'like', $searchTerm)
                    ->orWhere('governorate', 'like', $searchTerm);
                });
            });
        })
          ->orWhere('notes', 'like', $searchTerm)
          ->orWhere('bill_amount', 'like', $searchTerm);

        if ($parsedDate) {
          $query->orWhereDate('reading_date', $parsedDate->toDateString());
        }
      });
    }

    if ($filters['company_id']) {
      $readingsQuery->whereHas('electricityService', function ($serviceQuery) use ($filters) {
        $serviceQuery->where('electricity_company_id', $filters['company_id']);
      });
    }

    if ($filters['solar'] === 'with') {
      $readingsQuery->whereHas('electricityService', function ($serviceQuery) {
        $serviceQuery->where('has_solar_power', true);
      });
    } elseif ($filters['solar'] === 'without') {
      $readingsQuery->whereHas('electricityService', function ($serviceQuery) {
        $serviceQuery->where('has_solar_power', false);
      });
    }

    if ($filters['governorate']) {
      $readingsQuery->whereHas('electricityService.building.site', function ($siteQuery) use ($filters) {
        $siteQuery->where('governorate', $filters['governorate']);
      });
    }

    if ($filters['date_from']) {
      $readingsQuery->whereDate('reading_date', '>=', $filters['date_from']);
    }

    if ($filters['date_to']) {
      $readingsQuery->whereDate('reading_date', '<=', $filters['date_to']);
    }

    if ($filters['amount_min'] !== null && $filters['amount_min'] !== '') {
      $readingsQuery->where('bill_amount', '>=', $filters['amount_min']);
    }

    if ($filters['amount_max'] !== null && $filters['amount_max'] !== '') {
      $readingsQuery->where('bill_amount', '<=', $filters['amount_max']);
    }

    switch ($sort) {
      case 'service':
        $readingsQuery->join('electricity_services', 'electric_readings.electric_service_id', '=', 'electricity_services.id')
          ->orderBy('electricity_services.registration_number', $direction)
          ->select('electric_readings.*');
        break;
      case 'meter':
        $readingsQuery->join('electricity_services', 'electric_readings.electric_service_id', '=', 'electricity_services.id')
          ->orderBy('electricity_services.meter_number', $direction)
          ->select('electric_readings.*');
        break;
      case 'company':
        $readingsQuery->join('electricity_services', 'electric_readings.electric_service_id', '=', 'electricity_services.id')
          ->orderBy('electricity_services.company_name', $direction)
          ->select('electric_readings.*');
        break;
      case 'bill':
        $readingsQuery->orderBy('bill_amount', $direction);
        break;
      case 'consumption':
        $readingsQuery->orderBy('consumption_value', $direction);
        break;
      case 'number':
        $readingsQuery->orderBy('id', $direction);
        break;
      case 'date':
      default:
        $readingsQuery->orderBy('reading_date', $direction)->orderBy('id', $direction);
        break;
    }

    /** @var LengthAwarePaginator $readings */
    $readings = $readingsQuery->paginate(25)->withQueryString();

    $readings->setCollection(
      $readings->getCollection()->map(function (ElectricReading $reading) use ($computed) {
        $info = $computed[$reading->id] ?? [
          'previous_imported' => 0.0,
          'previous_produced' => 0.0,
          'consumption' => round((float) ($reading->consumption_value ?? 0), 2),
        ];

        $reading->setAttribute('computed_previous_imported', $info['previous_imported']);
        $reading->setAttribute('computed_previous_produced', $info['previous_produced']);
        $reading->setAttribute('computed_consumption', $info['consumption']);

        return $reading;
      })
    );

    $summaryBaseQuery = ElectricReading::query();

    $summary = [
      'total_outstanding' => (float) (clone $summaryBaseQuery)->where('is_paid', false)->sum('bill_amount'),
      'total_readings' => (clone $summaryBaseQuery)->count(),
      'unpaid_count' => (clone $summaryBaseQuery)->where('is_paid', false)->count(),
      'paid_count' => (clone $summaryBaseQuery)->where('is_paid', true)->count(),
      'unique_services' => (clone $summaryBaseQuery)->distinct('electric_service_id')->count('electric_service_id'),
      'total_consumption' => (float) (clone $summaryBaseQuery)->sum('consumption_value'),
    ];

    $filteredTotals = [
      'outstanding' => (float) $readings->getCollection()->where('is_paid', false)->sum(fn(ElectricReading $reading) => (float) ($reading->bill_amount ?? 0)),
      'paid' => (float) $readings->getCollection()->where('is_paid', true)->sum(fn(ElectricReading $reading) => (float) ($reading->bill_amount ?? 0)),
    ];

    $companies = ElectricityCompany::orderBy('name')
      ->get()
      ->mapWithKeys(function (ElectricityCompany $company) {
        $label = $company->name;

        if ($company->name_ar) {
          $label .= ' — ' . $company->name_ar;
        }

        return [$company->id => $label];
      });

    $governorates = Site::select('governorate')
      ->distinct()
      ->orderBy('governorate')
      ->get()
      ->mapWithKeys(function (Site $site) {
        $code = $site->governorate;
        $label = $site->governorate_name_en ?? $code;
        return [$code => $label];
      });

    return view('electricity.bills.index', [
      'readings' => $readings,
      'summary' => $summary,
      'filteredTotals' => $filteredTotals,
      'filters' => $filters,
      'sort' => $sort,
      'direction' => $direction,
      'companies' => $companies,
      'governorates' => $governorates,
    ]);
  }
}
