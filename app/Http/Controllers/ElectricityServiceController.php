<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElectricityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElectricityServiceController extends Controller
{
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
        $query = ElectricityService::with('building.site');

        // Apply search filter
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('registration_number', 'like', "%{$filters['search']}%")
                    ->orWhere('company_name', 'like', "%{$filters['search']}%")
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
            case 'registration':
                $query->orderBy('registration_number', $direction);
                break;
            case 'previous':
                $query->orderBy('previous_reading', $direction);
                break;
            case 'current':
                $query->orderBy('current_reading', $direction);
                break;
            case 'building':
                $query->join('buildings', 'electricity_services.building_id', '=', 'buildings.id')
                    ->orderBy('buildings.name', $direction)
                    ->select('electricity_services.*');
                break;
            case 'number':
            default:
                // For number column, reverse the direction for data sorting
                $actualDirection = $direction === 'asc' ? 'desc' : 'asc';
                $query->orderBy('id', $actualDirection);
                break;
        }

        // Get distinct companies for filter dropdown
        $companies = ElectricityService::distinct()
            ->orderBy('company_name')
            ->pluck('company_name', 'company_name');

        $electricityServices = $query->paginate(15)->withQueryString();

        return view('electricity-services.index', compact('electricityServices', 'companies', 'filters', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buildings = Building::with('site')->get();
        return view('electricity-services.create', compact('buildings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'company_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'previous_reading' => 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reset_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'remarks' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('reset_file')) {
            $validated['reset_file'] = $request->file('reset_file')->store('electricity-services/files', 'public');
        }

        ElectricityService::create($validated);
        return redirect()->route('electricity-services.index')->with('success', 'Electricity service record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ElectricityService $electricityService)
    {
        $electricityService->load('building.site');
        return view('electricity-services.show', compact('electricityService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ElectricityService $electricityService)
    {
        $buildings = Building::with('site')->get();
        return view('electricity-services.edit', compact('electricityService', 'buildings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectricityService $electricityService)
    {
        $validated = $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'company_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'previous_reading' => 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'reset_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'remarks' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('reset_file')) {
            $validated['reset_file'] = $request->file('reset_file')->store('electricity-services/files', 'public');
        }

        $electricityService->update($validated);
        return redirect()->route('electricity-services.index')->with('success', 'Electricity service record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectricityService $electricityService)
    {
        $electricityService->delete();
        return redirect()->route('electricity-services.index')->with('success', 'Electricity service record deleted successfully!');
    }

    /**
     * Display deleted electricity services
     */
    public function deleted()
    {
        $electricityServices = ElectricityService::onlyTrashed()
            ->with('building.site')
            ->latest('deleted_at')
            ->paginate(15);
        return view('electricity-services.deleted', compact('electricityServices'));
    }

    /**
     * Restore a soft deleted electricity service
     */
    public function restore($id)
    {
        $electricityService = ElectricityService::onlyTrashed()->findOrFail($id);
        $electricityService->restore();
        return redirect()->route('electricity-services.deleted')->with('success', 'Electricity service restored successfully!');
    }

    /**
     * Permanently delete an electricity service
     */
    public function forceDelete($id)
    {
        $electricityService = ElectricityService::onlyTrashed()->findOrFail($id);

        // Delete file if it exists
        if ($electricityService->reset_file && Storage::disk('public')->exists($electricityService->reset_file)) {
            Storage::disk('public')->delete($electricityService->reset_file);
        }

        $electricityService->forceDelete();
        return redirect()->route('electricity-services.deleted')->with('success', 'Electricity service permanently deleted!');
    }
}
