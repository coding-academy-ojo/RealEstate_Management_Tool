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
        $query = WaterService::with('building.site');

        // Apply search filter
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('registration_number', 'like', "%{$filters['search']}%")
                    ->orWhere('iron_number', 'like', "%{$filters['search']}%")
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
            case 'iron':
                $query->orderBy('iron_number', $direction);
                break;
            case 'previous':
                $query->orderBy('previous_reading', $direction);
                break;
            case 'current':
                $query->orderBy('current_reading', $direction);
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
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'previous_reading' => 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('invoice_file')) {
            $validated['invoice_file'] = $request->file('invoice_file')->store('water-services/invoices', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            $validated['payment_receipt'] = $request->file('payment_receipt')->store('water-services/receipts', 'public');
        }

        WaterService::create($validated);
        return redirect()->route('water-services.index')->with('success', 'Water service record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(WaterService $waterService)
    {
        $waterService->load('building.site');
        return view('water-services.show', compact('waterService'));
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
            'registration_number' => 'required|string|max:255',
            'iron_number' => 'nullable|string|max:255',
            'previous_reading' => 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'reading_date' => 'required|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'payment_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Handle file uploads
        if ($request->hasFile('invoice_file')) {
            $validated['invoice_file'] = $request->file('invoice_file')->store('water-services/invoices', 'public');
        }

        if ($request->hasFile('payment_receipt')) {
            $validated['payment_receipt'] = $request->file('payment_receipt')->store('water-services/receipts', 'public');
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
            ->with('building.site')
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
        $waterService = WaterService::onlyTrashed()->findOrFail($id);

        // Delete files if they exist
        if ($waterService->invoice_file && Storage::disk('public')->exists($waterService->invoice_file)) {
            Storage::disk('public')->delete($waterService->invoice_file);
        }
        if ($waterService->payment_receipt && Storage::disk('public')->exists($waterService->payment_receipt)) {
            Storage::disk('public')->delete($waterService->payment_receipt);
        }

        $waterService->forceDelete();
        return redirect()->route('water-services.deleted')->with('success', 'Water service permanently deleted!');
    }

    public function file(WaterService $waterService, string $document)
    {
        $documentMap = [
            'invoice' => 'invoice_file',
            'payment-receipt' => 'payment_receipt',
        ];

        if (!array_key_exists($document, $documentMap)) {
            abort(404);
        }

        $attribute = $documentMap[$document];
        $path = $waterService->{$attribute};

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File not found.');
        }

        $absolutePath = Storage::disk('public')->path($path);

        if (request()->boolean('download')) {
            return response()->download($absolutePath, basename($path));
        }

        $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
        ]);
    }
}
