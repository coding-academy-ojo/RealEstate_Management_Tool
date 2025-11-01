<?php

namespace App\Http\Controllers;

use App\Models\WaterReading;
use App\Models\WaterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaterReadingController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:water')->except(['index']);
  }

  public function index(Request $request)
  {
    $filters = [
      'status' => $request->input('status', 'all'),
      'search' => trim((string) $request->input('search')),
    ];

    $sort = $request->input('sort', 'date');
    $direction = $request->input('direction', 'desc');

    $allReadings = WaterReading::select('id', 'water_service_id', 'current_reading', 'reading_date', 'created_at')
      ->orderBy('reading_date')
      ->orderBy('id')
      ->get()
      ->groupBy('water_service_id');

    $computed = [];

    foreach ($allReadings as $serviceReadings) {
      $previous = 0.0;
      $isFirst = true;

      foreach ($serviceReadings as $reading) {
        $current = (float) ($reading->current_reading ?? 0);
        $consumption = $isFirst ? $current : max(0, round($current - $previous, 2));

        $computed[$reading->id] = [
          'previous' => round($previous, 2),
          'consumption' => round($consumption, 2),
        ];

        $previous = $current;
        $isFirst = false;
      }
    }

    $readingsQuery = WaterReading::with([
      'waterService.building.site',
      'waterService.waterCompany',
    ]);

    // Apply sorting
    switch ($sort) {
      case 'service':
        $readingsQuery->join('water_services', 'water_readings.water_service_id', '=', 'water_services.id')
          ->orderBy('water_services.registration_number', $direction)
          ->select('water_readings.*');
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

    if ($filters['status'] === 'paid') {
      $readingsQuery->where('is_paid', true);
    } elseif ($filters['status'] === 'unpaid') {
      $readingsQuery->where('is_paid', false);
    }

    $parsedDate = null;

    if ($filters['search']) {
      $searchTerm = '%' . $filters['search'] . '%';

      try {
        $parsedDate = Carbon::parse($filters['search']);
      } catch (\Exception $exception) {
        $parsedDate = null;
      }

      $readingsQuery->where(function ($query) use ($searchTerm, $parsedDate) {
        $query->whereHas('waterService', function ($serviceQuery) use ($searchTerm) {
          $serviceQuery->where('registration_number', 'like', $searchTerm)
            ->orWhere('meter_owner_name', 'like', $searchTerm)
            ->orWhere('company_name', 'like', $searchTerm)
            ->orWhere('company_name_ar', 'like', $searchTerm)
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

    $readings = $readingsQuery->paginate(25)->withQueryString();

    $readings->setCollection(
      $readings->getCollection()->map(function (WaterReading $reading) use ($computed) {
        $info = $computed[$reading->id] ?? ['previous' => 0.0, 'consumption' => 0.0];

        $reading->setAttribute('computed_previous_reading', $info['previous']);
        $reading->setAttribute('computed_consumption', $info['consumption']);

        return $reading;
      })
    );

    $summaryBaseQuery = WaterReading::query();

    $summary = [
      'total_outstanding' => (float) (clone $summaryBaseQuery)->where('is_paid', false)->sum('bill_amount'),
      'total_readings' => (clone $summaryBaseQuery)->count(),
      'unpaid_count' => (clone $summaryBaseQuery)->where('is_paid', false)->count(),
      'paid_count' => (clone $summaryBaseQuery)->where('is_paid', true)->count(),
      'unique_services' => (clone $summaryBaseQuery)->distinct('water_service_id')->count('water_service_id'),
      'total_consumption' => (float) (clone $summaryBaseQuery)->sum('consumption_value'),
    ];

    $filteredTotals = [
      'outstanding' => (float) $readings->getCollection()->where('is_paid', false)->sum(function (WaterReading $reading) {
        return (float) ($reading->bill_amount ?? 0);
      }),
      'paid' => (float) $readings->getCollection()->where('is_paid', true)->sum(function (WaterReading $reading) {
        return (float) ($reading->bill_amount ?? 0);
      }),
    ];

    return view('water.bills.index', [
      'readings' => $readings,
      'summary' => $summary,
      'filteredTotals' => $filteredTotals,
      'filters' => $filters,
    ]);
  }

  public function store(Request $request, WaterService $waterService)
  {
    $data = $this->validateReading($request);
    $data['water_service_id'] = $waterService->id;
    $data['is_paid'] = $request->boolean('is_paid');

    if ($request->hasFile('meter_image')) {
      $data['meter_image'] = $request->file('meter_image')->store('water-services/readings/meters', 'private');
    }

    if ($request->hasFile('bill_image')) {
      $data['bill_image'] = $request->file('bill_image')->store('water-services/readings/bills', 'private');
    }

    WaterReading::create($data);
    $this->recalculateReadings($waterService);

    $redirectUrl = $this->resolveRedirectUrl($request, route('water-services.show', $waterService));

    return redirect()->to($redirectUrl)
      ->with('success', 'Water reading added successfully.');
  }

  public function update(Request $request, WaterService $waterService, WaterReading $waterReading)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $data = $this->validateReading($request, $waterReading->id);
    $data['is_paid'] = $request->boolean('is_paid');

    if ($request->hasFile('meter_image')) {
      $this->deleteStoredFile($waterReading->meter_image);
      $data['meter_image'] = $request->file('meter_image')->store('water-services/readings/meters', 'private');
    }

    if ($request->hasFile('bill_image')) {
      $this->deleteStoredFile($waterReading->bill_image);
      $data['bill_image'] = $request->file('bill_image')->store('water-services/readings/bills', 'private');
    }

    $waterReading->update($data);
    $this->recalculateReadings($waterService);

    $redirectUrl = $this->resolveRedirectUrl($request, route('water-services.show', $waterService));

    return redirect()->to($redirectUrl)
      ->with('success', 'Water reading updated successfully.');
  }

  public function destroy(WaterService $waterService, WaterReading $waterReading)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $this->deleteStoredFile($waterReading->meter_image);
    $this->deleteStoredFile($waterReading->bill_image);

    $waterReading->delete();
    $this->recalculateReadings($waterService);

    $redirectUrl = $this->resolveRedirectUrl(request(), route('water-services.show', $waterService));

    return redirect()->to($redirectUrl)
      ->with('success', 'Water reading deleted successfully.');
  }

  public function file(WaterService $waterService, WaterReading $waterReading, string $document)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $attribute = match ($document) {
      'meter' => 'meter_image',
      'bill' => 'bill_image',
      default => abort(404),
    };

    $path = $waterReading->{$attribute};
    $disk = $this->resolveDiskForPath($path);

    if (!$path || !$disk) {
      abort(404, 'File not found.');
    }

    $absolutePath = Storage::disk($disk)->path($path);

    if (request()->boolean('download')) {
      return response()->download($absolutePath, basename($path));
    }

    $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

    return response()->file($absolutePath, [
      'Content-Type' => $mime,
    ]);
  }

  private function validateReading(Request $request, ?int $readingId = null): array
  {
    return $request->validate([
      'current_reading' => 'required|numeric|min:0',
      'bill_amount' => 'nullable|numeric|min:0',
      'is_paid' => 'nullable|boolean',
      'reading_date' => 'required|date',
      'meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
      'bill_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
      'notes' => 'nullable|string',
    ]);
  }

  private function recalculateReadings(WaterService $waterService): void
  {
    $readings = $waterService->readings()->orderBy('reading_date')->orderBy('id')->get();
    $previous = 0.0;
    $isFirst = true;

    foreach ($readings as $reading) {
      $current = (float) ($reading->current_reading ?? 0);
      $consumption = $isFirst
        ? $current
        : max(0, round($current - $previous, 2));

      $reading->forceFill([
        'consumption_value' => $consumption,
      ])->saveQuietly();

      $previous = $current;
      $isFirst = false;
    }
  }

  private function ensureBelongsToService(WaterService $waterService, WaterReading $waterReading): void
  {
    if ($waterReading->water_service_id !== $waterService->id) {
      abort(404);
    }
  }

  private function deleteStoredFile(?string $path): void
  {
    $disk = $this->resolveDiskForPath($path);

    if ($disk) {
      try {
        Storage::disk($disk)->delete($path);
      } catch (\Throwable $exception) {
        // Ignore disk errors during cleanup
      }
    }
  }

  private function resolveDiskForPath(?string $path): ?string
  {
    if (!$path) {
      return null;
    }

    foreach (['private', 'public'] as $disk) {
      try {
        if (Storage::disk($disk)->exists($path)) {
          return $disk;
        }
      } catch (\Throwable $exception) {
        // Skip disks that are not configured
      }
    }

    return null;
  }

  private function resolveRedirectUrl(Request $request, string $fallback): string
  {
    $redirect = trim((string) $request->input('redirect_to'));

    if ($redirect && str_starts_with($redirect, url('/'))) {
      return $redirect;
    }

    return $fallback;
  }
}
