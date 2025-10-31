<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElectricReading;
use App\Models\ElectricServiceDisconnection;
use App\Models\ElectricityCompany;
use App\Models\ElectricityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ElectricityServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:electricity')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'company_id' => $request->input('company'),
        ];

        $sort = $request->input('sort', 'number');
        $direction = $request->input('direction', 'asc');

        $query = ElectricityService::with(['building.site', 'latestReading', 'electricityCompany'])
            ->withCount([
                'disconnections as open_disconnections_count' => fn($query) => $query
                    ->whereNull('reconnection_date')
                    ->whereNull('deleted_at'),
            ]);

        if ($filters['search']) {
            $searchTerm = "%{$filters['search']}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('registration_number', 'like', $searchTerm)
                    ->orWhere('company_name', 'like', $searchTerm)
                    ->orWhere('company_name_ar', 'like', $searchTerm)
                    ->orWhere('subscriber_name', 'like', $searchTerm)
                    ->orWhere('meter_number', 'like', $searchTerm)
                    ->orWhereHas('building', fn($b) => $b->where('name', 'like', $searchTerm));
            });
        }

        if ($filters['company_id']) {
            $query->where('electricity_company_id', $filters['company_id']);
        }

        switch ($sort) {
            case 'company':
                $query->orderBy('company_name', $direction);
                break;
            case 'subscriber':
                $query->orderBy('subscriber_name', $direction);
                break;
            case 'registration':
                $query->orderBy('registration_number', $direction);
                break;
            case 'meter':
                $query->orderBy('meter_number', $direction);
                break;
            case 'building':
                $query->join('buildings', 'electricity_services.building_id', '=', 'buildings.id')
                    ->orderBy('buildings.name', $direction)
                    ->select('electricity_services.*');
                break;
            case 'number':
            default:
                $actualDirection = $direction === 'asc' ? 'desc' : 'asc';
                $query->orderBy('id', $actualDirection);
                break;
        }

        $companies = ElectricityCompany::orderBy('name')
            ->get()
            ->mapWithKeys(function (ElectricityCompany $company) {
                $label = $company->name;

                if ($company->name_ar) {
                    $label .= ' — ' . $company->name_ar;
                }

                return [$company->id => $label];
            });

        $electricityServices = $query->paginate(15)->withQueryString();

        return view('electric.index', compact('electricityServices', 'companies', 'filters', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buildings = Building::with('site')->get();
        $electricityCompanies = ElectricityCompany::orderBy('name')->get();

        return view('electric.create', compact('buildings', 'electricityCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'subscriber_name' => 'required|string|max:255',
            'meter_number' => 'required|string|max:255|unique:electricity_services,meter_number',
            'has_solar_power' => 'nullable|boolean',
            'electricity_company_id' => 'required|exists:electricity_companies,id',
            'registration_number' => 'required|string|max:255',
            'reset_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'remarks' => 'nullable|string',
        ]);

        $validated['has_solar_power'] = $request->boolean('has_solar_power');

        if ($request->hasFile('reset_file')) {
            $validated['reset_file'] = $request->file('reset_file')->store('electricity-services/files', 'private');
        }

        $company = ElectricityCompany::findOrFail($validated['electricity_company_id']);
        $validated['company_name'] = $company->name;
        $validated['company_name_ar'] = $company->name_ar;

        ElectricityService::create($validated);

        return redirect()
            ->route('electricity-services.index')
            ->with('success', 'Electricity service record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ElectricityService $electricityService)
    {
        $electricityService->load([
            'building.site',
            'electricityCompany',
            'readings' => fn($query) => $query->orderByDesc('reading_date')->orderByDesc('id'),
            'latestReading',
            'disconnections' => fn($query) => $query->orderByDesc('disconnection_date')->orderByDesc('id'),
        ]);

        $sortKey = static function ($reading) {
            $base = $reading->reading_date?->timestamp ?? $reading->created_at?->timestamp ?? 0;
            return ($base * 1_000_000) + $reading->id;
        };

        $displayReadings = $electricityService->readings
            ->sortByDesc($sortKey)
            ->values();

        $chronological = $displayReadings
            ->reverse()
            ->values();

        $previousById = [];
        $previousImportedCalculated = 0.0;
        $previousProducedCalculated = 0.0;
        $isSolar = $electricityService->has_solar_power;

        foreach ($chronological as $reading) {
            $previousById[$reading->id] = [
                'imported_calculated' => round($previousImportedCalculated, 2),
                'produced_calculated' => round($previousProducedCalculated, 2),
            ];

            $previousImportedCalculated = (float) ($reading->imported_calculated ?? 0);
            $previousProducedCalculated = (float) ($reading->produced_calculated ?? 0);
        }

        $readings = $displayReadings->map(function (ElectricReading $reading) use ($previousById) {
            $previous = $previousById[$reading->id] ?? [
                'imported_calculated' => 0.0,
                'produced_calculated' => 0.0,
            ];

            $reading->setAttribute('computed_previous_imported_calculated', $previous['imported_calculated']);
            $reading->setAttribute('computed_previous_produced_calculated', $previous['produced_calculated']);
            $reading->setAttribute('computed_consumption', round((float) ($reading->consumption_value ?? 0), 2));
            return $reading;
        });

        $latestReading = $electricityService->latestReading;

        $openDisconnection = $electricityService->disconnections
            ->firstWhere(fn(ElectricServiceDisconnection $record) => $record->reconnection_date === null);

        $unpaidCount = $readings->where('is_paid', false)->count();

        return view('electric.show', [
            'electricityService' => $electricityService,
            'readings' => $readings,
            'latestReading' => $latestReading,
            'openDisconnection' => $openDisconnection,
            'unpaidCount' => $unpaidCount,
            'latestIndicator' => $latestReading
                ? ($isSolar
                    ? round(
                        (float) (($latestReading->produced_calculated ?? 0) - ($latestReading->imported_calculated ?? 0)),
                        2
                    )
                    : round((float) ($latestReading->imported_calculated ?? 0), 2))
                : null,
            'latestImportedCurrent' => $latestReading?->imported_current,
            'latestImportedCalculated' => $latestReading?->imported_calculated,
            'latestProducedCurrent' => $latestReading?->produced_current,
            'latestProducedCalculated' => $latestReading?->produced_calculated,
            'latestSavedEnergy' => $latestReading?->saved_energy,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ElectricityService $electricityService)
    {
        $electricityService->load('building.site', 'electricityCompany');
        $buildings = Building::with('site')->get();
        $electricityCompanies = ElectricityCompany::orderBy('name')->get();

        return view('electric.edit', compact('electricityService', 'buildings', 'electricityCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectricityService $electricityService)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'subscriber_name' => 'required|string|max:255',
            'meter_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('electricity_services', 'meter_number')->ignore($electricityService->id),
            ],
            'has_solar_power' => 'nullable|boolean',
            'electricity_company_id' => 'required|exists:electricity_companies,id',
            'registration_number' => 'required|string|max:255',
            'reset_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'remarks' => 'nullable|string',
        ]);

        $validated['has_solar_power'] = $request->boolean('has_solar_power');

        if ($request->hasFile('reset_file')) {
            $this->deleteStoredFile($electricityService->reset_file);
            $validated['reset_file'] = $request->file('reset_file')->store('electricity-services/files', 'private');
        }

        $company = ElectricityCompany::findOrFail($validated['electricity_company_id']);
        $validated['company_name'] = $company->name;
        $validated['company_name_ar'] = $company->name_ar;

        $electricityService->update($validated);

        return redirect()
            ->route('electricity-services.index')
            ->with('success', 'Electricity service record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectricityService $electricityService)
    {
        $electricityService->delete();

        return redirect()
            ->route('electricity-services.index')
            ->with('success', 'Electricity service record deleted successfully!');
    }

    /**
     * Display deleted electricity services.
     */
    public function deleted()
    {
        $electricityServices = ElectricityService::onlyTrashed()
            ->with(['building.site', 'latestReading', 'electricityCompany'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('electric.deleted', compact('electricityServices'));
    }

    /**
     * Restore a soft-deleted electricity service.
     */
    public function restore($id)
    {
        $electricityService = ElectricityService::onlyTrashed()->findOrFail($id);
        $electricityService->restore();

        return redirect()
            ->route('electricity-services.deleted')
            ->with('success', 'Electricity service restored successfully!');
    }

    /**
     * Permanently delete an electricity service.
     */
    public function forceDelete($id)
    {
        $electricityService = ElectricityService::onlyTrashed()
            ->with(['readings', 'disconnections'])
            ->findOrFail($id);

        $this->deleteStoredFile($electricityService->reset_file);

        foreach ($electricityService->readings as $reading) {
            $this->deleteStoredFile($reading->meter_image);
            $this->deleteStoredFile($reading->bill_image);
        }

        $electricityService->forceDelete();

        return redirect()
            ->route('electricity-services.deleted')
            ->with('success', 'Electricity service permanently deleted!');
    }

    public function file(ElectricityService $electricityService, string $document)
    {
        abort_unless($document === 'reset', 404);

        $path = $electricityService->reset_file;
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

    public function deactivate(Request $request, ElectricityService $electricityService)
    {
        $validated = $request->validate([
            'deactivation_reason' => 'required|in:cancelled,meter_changed,merged,other',
            'deactivation_date' => 'required|date',
        ]);

        $electricityService->deactivate(
            $validated['deactivation_reason'],
            $validated['deactivation_date']
        );

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Electricity service has been deactivated successfully.');
    }

    public function reactivate(ElectricityService $electricityService)
    {
        $electricityService->reactivate();

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Electricity service has been reactivated successfully.');
    }
}
