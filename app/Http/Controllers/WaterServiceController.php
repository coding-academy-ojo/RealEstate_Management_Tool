<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Site;
use App\Models\WaterCompany;
use App\Models\WaterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaterServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:water')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [
            'search' => $request->input('search'),
            'company_id' => $request->input('company'),
            'governorate' => $request->input('governorate'),
            'status' => $request->input('status', 'all'),
        ];

        // Get sort parameters
        $sort = $request->input('sort', 'number');
        $direction = $request->input('direction', 'asc');

        // Build query
        $query = WaterService::with(['building.site', 'latestReading', 'waterCompany']);

        // Apply search filter
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('registration_number', 'like', "%{$filters['search']}%")
                    ->orWhere('iron_number', 'like', "%{$filters['search']}%")
                    ->orWhere('company_name', 'like', "%{$filters['search']}%")
                    ->orWhere('company_name_ar', 'like', "%{$filters['search']}%")
                    ->orWhere('meter_owner_name', 'like', "%{$filters['search']}%")
                    ->orWhereHas('building', function ($buildingQuery) use ($filters) {
                        $buildingQuery->where('name', 'like', "%{$filters['search']}%");
                    });
            });
        }

        // Apply company filter
        if ($filters['company_id']) {
            $query->where('water_company_id', $filters['company_id']);
        }

        // Apply governorate filter
        if ($filters['governorate']) {
            $query->whereHas('building.site', function ($siteQuery) use ($filters) {
                $siteQuery->where('governorate', $filters['governorate']);
            });
        }

        // Apply status filter
        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'inactive') {
            $query->where('is_active', false);
        }

        // Apply sorting
        switch ($sort) {
            case 'company':
                $query->orderBy('company_name', $direction);
                break;
            case 'owner':
                $query->orderBy('meter_owner_name', $direction);
                break;
            case 'registration':
                $query->orderBy('registration_number', $direction);
                break;
            case 'iron':
                $query->orderBy('iron_number', $direction);
                break;
            case 'building':
                $query->join('buildings', 'water_services.building_id', '=', 'buildings.id')
                    ->orderBy('buildings.name', $direction)
                    ->select('water_services.*');
                break;
            case 'number':
            default:
                // For number column, reverse the direction for data sorting
                $actualDirection = $direction === 'asc' ? 'desc' : 'asc';
                $query->orderBy('id', $actualDirection);
                break;
        }

        // Get distinct companies for filter dropdown
        $companies = WaterCompany::orderBy('name')
            ->get()
            ->mapWithKeys(function (WaterCompany $company) {
                $label = $company->name;

                if ($company->name_ar) {
                    $label .= ' — ' . $company->name_ar;
                }

                return [$company->id => $label];
            });

        $waterServices = $query->paginate(15)->withQueryString();

        $governorates = Site::select('governorate')
            ->distinct()
            ->orderBy('governorate')
            ->get()
            ->mapWithKeys(function (Site $site) {
                $code = $site->governorate;
                $label = $site->governorate_name_en ?? $code;
                return [$code => $label];
            });

        return view('water-services.index', compact('waterServices', 'companies', 'filters', 'sort', 'direction', 'governorates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buildings = Building::with('site')->get();
        $waterCompanies = WaterCompany::orderBy('name')->get();

        return view('water-services.create', compact('buildings', 'waterCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'water_company_id' => 'required|exists:water_companies,id',
            'meter_owner_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'initial_meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('initial_meter_image')) {
            $validated['initial_meter_image'] = $request->file('initial_meter_image')->store('water-services/reference-meters', 'private');
        }

        $company = WaterCompany::findOrFail($validated['water_company_id']);
        $validated['company_name'] = $company->name;
        $validated['company_name_ar'] = $company->name_ar;

        WaterService::create($validated);
        return redirect()->route('water.services.index')->with('success', 'Water service record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterService $waterService)
    {
        $waterService->load([
            'building.site',
            'waterCompany',
            'readings' => fn($query) => $query->orderByDesc('reading_date')->orderByDesc('id'),
            'latestReading',
        ]);

        $sortKey = static function ($reading) {
            $base = $reading->reading_date?->timestamp ?? $reading->created_at?->timestamp ?? 0;
            return ($base * 1_000_000) + $reading->id;
        };

        $displayReadings = $waterService->readings
            ->sortByDesc($sortKey)
            ->values();

        $chronological = $displayReadings
            ->reverse()
            ->values();

        $computedById = [];
        $previousValue = 0.0;

        foreach ($chronological as $reading) {
            $currentValue = (float) ($reading->current_reading ?? 0);
            $consumption = max(0.0, round($currentValue - $previousValue, 2));

            $computedById[$reading->id] = [
                'previous' => round($previousValue, 2),
                'consumption' => round($consumption, 2),
            ];

            $previousValue = round($currentValue, 2);
        }

        $readings = $displayReadings->map(function ($reading) use ($computedById) {
            $info = $computedById[$reading->id] ?? ['previous' => 0.0, 'consumption' => 0.0];
            $reading->setAttribute('computed_previous_reading', $info['previous']);
            $reading->setAttribute('computed_consumption', $info['consumption']);
            return $reading;
        });

        $latestReading = $readings->first();

        $totalConsumption = collect($computedById)->sum('consumption');

        $outstandingAmount = $readings
            ->where('is_paid', false)
            ->sum(function ($reading) {
                return (float) ($reading->bill_amount ?? 0);
            });

        return view('water-services.show', compact('waterService', 'readings', 'latestReading', 'totalConsumption', 'outstandingAmount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterService $waterService)
    {
        $buildings = Building::with('site')->get();
        $waterCompanies = WaterCompany::orderBy('name')->get();

        return view('water-services.edit', compact('waterService', 'buildings', 'waterCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WaterService $waterService)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'water_company_id' => 'required|exists:water_companies,id',
            'meter_owner_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'initial_meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('initial_meter_image')) {
            $this->deleteStoredFile($waterService->initial_meter_image);
            $validated['initial_meter_image'] = $request->file('initial_meter_image')->store('water-services/reference-meters', 'private');
        }

        $company = WaterCompany::findOrFail($validated['water_company_id']);
        $validated['company_name'] = $company->name;
        $validated['company_name_ar'] = $company->name_ar;

        $waterService->update($validated);
        return redirect()->route('water.services.index')->with('success', 'Water service record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaterService $waterService)
    {
        $waterService->delete();
        return redirect()->route('water.services.index')->with('success', 'Water service record deleted successfully!');
    }

    /**
     * Deactivate the water service
     */
    public function deactivate(Request $request, WaterService $waterService)
    {
        $validated = $request->validate([
            'deactivation_reason' => 'required|in:cancelled,meter_changed,merged,other',
            'deactivation_date' => 'required|date',
        ]);

        $waterService->deactivate(
            $validated['deactivation_reason'],
            $validated['deactivation_date']
        );

        return redirect()->route('water-services.show', $waterService)
            ->with('success', 'Water service has been deactivated successfully!');
    }

    /**
     * Reactivate the water service
     */
    public function reactivate(WaterService $waterService)
    {
        $waterService->reactivate();

        return redirect()->route('water-services.show', $waterService)
            ->with('success', 'Water service has been reactivated successfully!');
    }

    /**
     * Display deleted water services
     */
    public function deleted()
    {
        $waterServices = WaterService::onlyTrashed()
            ->with(['building.site', 'latestReading', 'waterCompany'])
            ->latest('deleted_at')
            ->paginate(15);
        return view('water-services.deleted', compact('waterServices'));
    }

    /**
     * Restore a soft deleted water service
     */
    public function restore($id)
    {
        $waterService = WaterService::onlyTrashed()->findOrFail($id);
        $waterService->restore();
        return redirect()->route('water-services.deleted')->with('success', 'Water service restored successfully!');
    }

    /**
     * Permanently delete a water service
     */
    public function forceDelete($id)
    {
        $waterService = WaterService::onlyTrashed()
            ->with('readings')
            ->findOrFail($id);

        $this->deleteStoredFile($waterService->initial_meter_image);

        foreach ($waterService->readings as $reading) {
            $this->deleteStoredFile($reading->meter_image);
            $this->deleteStoredFile($reading->bill_image);
        }

        $waterService->forceDelete();
        return redirect()->route('water-services.deleted')->with('success', 'Water service permanently deleted!');
    }

    public function file(WaterService $waterService, string $document)
    {
        abort_unless($document === 'reference-meter', 404);

        $path = $waterService->initial_meter_image;
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
}
