<?php

namespace App\Http\Controllers;

use App\Models\ElectricityService;
use App\Models\ElectricReading;
use App\Services\ElectricReadingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectricityReadingBulkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:electricity']);
    }

    /**
     * Display the bulk readings page.
     */
    public function index()
    {
        return view('electric.bulk-readings');
    }

    /**
     * Search for electricity services.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $services = ElectricityService::with(['building.site', 'electricityCompany', 'latestReading'])
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('registration_number', 'like', "%{$query}%")
                    ->orWhere('meter_number', 'like', "%{$query}%")
                    ->orWhere('subscriber_name', 'like', "%{$query}%")
                    ->orWhereHas('building', function ($buildingQuery) use ($query) {
                        $buildingQuery->where('name', 'like', "%{$query}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'registration_number' => $service->registration_number,
                    'meter_number' => $service->meter_number,
                    'subscriber_name' => $service->subscriber_name,
                    'company_name' => optional($service->electricityCompany)->name ?? $service->company_name,
                    'building_name' => optional($service->building)->name,
                    'has_solar_power' => $service->has_solar_power,
                ];
            });

        return response()->json($services);
    }

    /**
     * Get service details.
     */
    public function show(ElectricityService $service)
    {
        $service->load(['building', 'electricityCompany', 'latestReading']);

        return response()->json([
            'id' => $service->id,
            'registration_number' => $service->registration_number,
            'meter_number' => $service->meter_number,
            'subscriber_name' => $service->subscriber_name,
            'company_name' => optional($service->electricityCompany)->name ?? $service->company_name,
            'building_name' => optional($service->building)->name,
            'has_solar_power' => $service->has_solar_power,
            'latest_reading' => $service->latestReading ? [
                'imported_current' => $service->latestReading->imported_current,
                'imported_calculated' => $service->latestReading->imported_calculated,
                'produced_current' => $service->latestReading->produced_current,
                'produced_calculated' => $service->latestReading->produced_calculated,
                'saved_energy' => $service->latestReading->saved_energy,
                'reading_date' => $service->latestReading->reading_date?->format('Y-m-d'),
            ] : null,
        ]);
    }

    /**
     * Store bulk readings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'readings' => 'required|array|min:1',
            'readings.*.service_id' => 'required|exists:electricity_services,id',
            'readings.*.reading_date' => 'required|date',
            'readings.*.imported_current' => 'nullable|numeric|min:0',
            'readings.*.imported_calculated' => 'nullable|numeric|min:0',
            'readings.*.produced_current' => 'nullable|numeric|min:0',
            'readings.*.produced_calculated' => 'nullable|numeric|min:0',
            'readings.*.saved_energy' => 'nullable|numeric|min:0',
            'readings.*.bill_amount' => 'nullable|numeric|min:0',
            'readings.*.is_paid' => 'required|boolean',
            'readings.*.notes' => 'nullable|string|max:1000',
        ]);

        $serviceIds = collect($validated['readings'])->pluck('service_id')->unique();
        $services = ElectricityService::whereIn('id', $serviceIds)->get()->keyBy('id');

        DB::transaction(function () use ($validated) {
            foreach ($validated['readings'] as $readingData) {
                ElectricReading::create([
                    'electric_service_id' => $readingData['service_id'],
                    'reading_date' => $readingData['reading_date'],
                    'imported_current' => $readingData['imported_current'],
                    'imported_calculated' => $readingData['imported_calculated'],
                    'produced_current' => $readingData['produced_current'],
                    'produced_calculated' => $readingData['produced_calculated'],
                    'saved_energy' => $readingData['saved_energy'],
                    'bill_amount' => $readingData['bill_amount'],
                    'is_paid' => $readingData['is_paid'],
                    'notes' => $readingData['notes'],
                ]);
            }
        });

        foreach ($services as $service) {
            ElectricReadingManager::recalculate($service->fresh());
        }

        return response()->json([
            'success' => true,
            'message' => count($validated['readings']) . ' reading(s) added successfully.',
        ]);
    }
}
