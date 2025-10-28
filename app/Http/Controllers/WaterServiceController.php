<?php

namespace App\Http\Controllers;

use App\Models\Building;
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
            'company' => $request->input('company'),
        ];

        // Get sort parameters
        $sort = $request->input('sort', 'number');
        $direction = $request->input('direction', 'asc');

        // Build query
        $query = WaterService::with(['building.site', 'latestReading']);

        // Apply search filter
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('registration_number', 'like', "%{$filters['search']}%")
                    ->orWhere('iron_number', 'like', "%{$filters['search']}%")
                    ->orWhere('company_name', 'like', "%{$filters['search']}%")
                    ->orWhere('meter_owner_name', 'like', "%{$filters['search']}%")
                    ->orWhereHas('building', function ($buildingQuery) use ($filters) {
                        $buildingQuery->where('name', 'like', "%{$filters['search']}%");
                    });
            });
        }

        // Apply company filter
        if ($filters['company']) {
            $query->where('company_name', $filters['company']);
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
        $companies = WaterService::distinct()
            ->orderBy('company_name')
            ->pluck('company_name', 'company_name');

        $waterServices = $query->paginate(15)->withQueryString();

        return view('water-services.index', compact('waterServices', 'companies', 'filters', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buildings = Building::with('site')->get();
        return view('water-services.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'company_name' => 'required|string|max:255',
            'meter_owner_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'initial_meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('initial_meter_image')) {
            $validated['initial_meter_image'] = $request->file('initial_meter_image')->store('water-services/reference-meters', 'public');
        }

        WaterService::create($validated);
        return redirect()->route('water-services.index')->with('success', 'Water service record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterService $waterService)
    {
        $waterService->load([
            'building.site',
            'readings' => fn($query) => $query->orderByDesc('reading_date')->orderByDesc('id'),
        ]);

        $readings = $waterService->readings;
        $latestReading = $readings->first();
        $totalConsumption = $readings->sum('consumption_value');
        $outstandingAmount = $readings->where('is_paid', false)->sum('bill_amount');

        return view('water-services.show', compact('waterService', 'readings', 'latestReading', 'totalConsumption', 'outstandingAmount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterService $waterService)
    {
        $buildings = Building::with('site')->get();
        return view('water-services.edit', compact('waterService', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WaterService $waterService)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'company_name' => 'required|string|max:255',
            'meter_owner_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'initial_meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('initial_meter_image')) {
            $this->deleteStoredFile($waterService->initial_meter_image);
            $validated['initial_meter_image'] = $request->file('initial_meter_image')->store('water-services/reference-meters', 'public');
        }

        $waterService->update($validated);
        return redirect()->route('water-services.index')->with('success', 'Water service record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaterService $waterService)
    {
        $waterService->delete();
        return redirect()->route('water-services.index')->with('success', 'Water service record deleted successfully!');
    }

    /**
     * Display deleted water services
     */
    public function deleted()
    {
        $waterServices = WaterService::onlyTrashed()
            ->with(['building.site', 'latestReading'])
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

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
