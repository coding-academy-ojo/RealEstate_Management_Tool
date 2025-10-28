<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\ZoningStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:sites_lands_buildings')->except(['index', 'show', 'getNextCluster']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'region',
            'governorate',
            'zoning',
        ]);

        $query = Site::query()
            ->with(['buildings', 'lands', 'zoningStatuses'])
            ->withCount(['buildings', 'lands']);

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['region'])) {
            $query->where('region', $filters['region']);
        }

        if (!empty($filters['governorate'])) {
            $query->where('governorate', $filters['governorate']);
        }

        if (!empty($filters['zoning'])) {
            $query->whereHas('zoningStatuses', function ($zoningQuery) use ($filters) {
                $zoningQuery->where('id', $filters['zoning']);
            });
        }

        $sort = $request->query('sort', 'number');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'code' => 'code',
            'name' => 'name',
            'governorate' => 'governorate',
            'area' => 'area_m2',
        ];

        if ($sort === 'number') {
            $query->orderBy('id', $direction === 'asc' ? 'desc' : 'asc');
        } elseif ($sort === 'buildings') {
            $query->orderBy('buildings_count', $direction);
        } elseif ($sort === 'lands') {
            $query->orderBy('lands_count', $direction);
        } elseif (array_key_exists($sort, $sortMap)) {
            $query->orderBy($sortMap[$sort], $direction);
        } else {
            $query->orderBy('id', 'desc');
            $sort = 'number';
            $direction = 'asc';
        }

        $sites = $query->paginate(15)->appends($request->query());

        $regions = [
            '1' => 'Capital',
            '2' => 'North',
            '3' => 'Middle',
            '4' => 'South',
        ];

        $governorates = [
            'AM' => 'Amman',
            'IR' => 'Irbid',
            'MF' => 'Mafraq',
            'AJ' => 'Ajloun',
            'JA' => 'Jerash',
            'BA' => 'Balqa',
            'ZA' => 'Zarqa',
            'MA' => 'Madaba',
            'AQ' => 'Aqaba',
            'KA' => 'Karak',
            'TF' => 'Tafileh',
            'MN' => "Ma'an",
        ];

        $zoningStatuses = ZoningStatus::active()->orderBy('name_ar')->get();

        return view('sites.index', [
            'sites' => $sites,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'regions' => $regions,
            'governorates' => $governorates,
            'zoningStatuses' => $zoningStatuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zoningStatuses = ZoningStatus::active()->orderBy('name_ar')->get();
        return view('sites.create', compact('zoningStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cluster_no' => 'required|integer|min:1',
            'governorate' => 'required|string|max:3',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'zoning_statuses' => 'nullable|array',
            'zoning_statuses.*' => 'exists:zoning_statuses,id',
            'notes' => 'nullable|string',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'document_names.*' => 'nullable|string|max:255',
            // Lands validation
            'lands' => 'nullable|array',
            'lands.*.directorate' => 'required_with:lands|string|max:255',
            'lands.*.directorate_number' => 'required_with:lands|string|max:255',
            'lands.*.village' => 'nullable|string|max:255',
            'lands.*.village_number' => 'nullable|string|max:255',
            'lands.*.basin' => 'required_with:lands|string|max:255',
            'lands.*.basin_number' => 'required_with:lands|string|max:255',
            'lands.*.neighborhood' => 'nullable|string|max:255',
            'lands.*.neighborhood_number' => 'nullable|string|max:255',
            'lands.*.plot_number' => 'required_with:lands|string|max:255',
            'lands.*.plot_key' => 'required_with:lands|string|max:255',
            'lands.*.area_m2' => 'required_with:lands|numeric|min:0',
            'lands.*.map_location' => 'nullable|url',
            'lands.*.latitude' => 'nullable|numeric|between:-90,90',
            'lands.*.longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Auto-determine region based on governorate
        $validated['region'] = $this->getRegionFromGovernorate($validated['governorate']);

        // Keep old zoning_status field for backward compatibility
        $validated['zoning_status'] = null;

        // Handle other documents file uploads with names
        $otherDocuments = [];
        if ($request->hasFile('other_documents')) {
            $documentNames = $request->input('document_names', []);
            foreach ($request->file('other_documents') as $index => $file) {
                $path = $file->store('sites/other_documents', 'public');
                $otherDocuments[] = [
                    'name' => $documentNames[$index] ?? 'Document ' . ($index + 1),
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }
        $validated['other_documents'] = $otherDocuments;

        $site = Site::create($validated);

        // Attach zoning statuses if provided
        if (!empty($validated['zoning_statuses'])) {
            $site->zoningStatuses()->attach($validated['zoning_statuses']);
        }

        // Create lands if provided
        if (!empty($validated['lands'])) {
            foreach ($validated['lands'] as $landData) {
                // Add site_id and governorate/region to each land
                $landData['site_id'] = $site->id;
                $landData['governorate'] = $validated['governorate'];
                $landData['region'] = $validated['region'];

                // Create the land
                $site->lands()->create($landData);
            }

            // Update site total area based on lands
            $totalLandArea = collect($validated['lands'])->sum('area_m2');
            if ($totalLandArea > 0) {
                $site->update(['area_m2' => $totalLandArea]);
            }
        }

        return redirect()->route('sites.index')->with('success', 'Site created successfully with ' . count($validated['lands'] ?? []) . ' land(s)!');
    }
    /**
     * Get region number from governorate code
     */
    private function getRegionFromGovernorate(string $governorate): int
    {
        return match ($governorate) {
            'AM' => 1,  // Capital
            'IR', 'MF', 'AJ', 'JA' => 2,  // North
            'BA', 'ZA', 'MA' => 3,  // Middle
            'AQ', 'KA', 'TF', 'MN' => 4,  // South
            default => 1
        };
    }

    /**
     * Get next cluster number for a governorate (API endpoint)
     */
    public function getNextCluster(string $governorate)
    {
        $lastSite = Site::where('governorate', $governorate)
            ->orderBy('cluster_no', 'desc')
            ->first();

        $nextCluster = $lastSite ? $lastSite->cluster_no + 1 : 1;

        return response()->json(['next_cluster' => $nextCluster]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        $site->load(['buildings.images', 'lands.images', 'rennovations', 'zoningStatuses', 'images']);
        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        $zoningStatuses = ZoningStatus::active()->orderBy('name_ar')->get();
        return view('sites.edit', compact('site', 'zoningStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'cluster_no' => 'required|integer|min:1',
            'governorate' => 'required|string|max:3',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'zoning_statuses' => 'nullable|array',
            'zoning_statuses.*' => 'exists:zoning_statuses,id',
            'notes' => 'nullable|string',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'document_names.*' => 'nullable|string|max:255',
            'remove_documents' => 'nullable|array',
            // Lands validation
            'lands' => 'nullable|array',
            'lands.*.id' => 'nullable|exists:lands,id',
            'lands.*._action' => 'nullable|in:create,update',
            'lands.*.directorate' => 'required_with:lands|string|max:255',
            'lands.*.directorate_number' => 'required_with:lands|string|max:255',
            'lands.*.village' => 'nullable|string|max:255',
            'lands.*.village_number' => 'nullable|string|max:255',
            'lands.*.basin' => 'required_with:lands|string|max:255',
            'lands.*.basin_number' => 'required_with:lands|string|max:255',
            'lands.*.neighborhood' => 'nullable|string|max:255',
            'lands.*.neighborhood_number' => 'nullable|string|max:255',
            'lands.*.plot_number' => 'required_with:lands|string|max:255',
            'lands.*.plot_key' => 'required_with:lands|string|max:255',
            'lands.*.area_m2' => 'required_with:lands|numeric|min:0',
            'lands.*.map_location' => 'nullable|url',
            'lands.*.latitude' => 'nullable|numeric|between:-90,90',
            'lands.*.longitude' => 'nullable|numeric|between:-180,180',
            'delete_lands' => 'nullable|array',
            'delete_lands.*' => 'exists:lands,id',
        ]);

        // Handle other documents
        $currentDocuments = $site->other_documents ?? [];

        // Remove selected documents
        if (!empty($validated['remove_documents'])) {
            foreach ($validated['remove_documents'] as $index) {
                if (isset($currentDocuments[$index]) && isset($currentDocuments[$index]['path'])) {
                    // Delete file from storage
                    Storage::disk('public')->delete($currentDocuments[$index]['path']);
                    unset($currentDocuments[$index]);
                }
            }
            // Re-index array
            $currentDocuments = array_values($currentDocuments);
        }

        // Add new documents with names
        if ($request->hasFile('other_documents')) {
            $documentNames = $request->input('document_names', []);
            foreach ($request->file('other_documents') as $index => $file) {
                $path = $file->store('sites/other_documents', 'public');
                $currentDocuments[] = [
                    'name' => $documentNames[$index] ?? 'Document ' . ($index + 1),
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }

        $validated['other_documents'] = $currentDocuments;

        $site->update($validated);

        // Sync zoning statuses
        if (isset($validated['zoning_statuses'])) {
            $site->zoningStatuses()->sync($validated['zoning_statuses']);
        } else {
            $site->zoningStatuses()->detach();
        }

        // Handle lands deletion
        if (!empty($validated['delete_lands'])) {
            foreach ($validated['delete_lands'] as $landId) {
                $land = $site->lands()->find($landId);
                if ($land) {
                    $land->delete();
                }
            }
        }

        // Handle lands update/creation
        if (!empty($validated['lands'])) {
            foreach ($validated['lands'] as $landData) {
                $action = $landData['_action'] ?? 'create';
                $landId = $landData['id'] ?? null;

                // Remove action field from data
                unset($landData['_action']);
                unset($landData['id']);

                // Add site_id and governorate/region to each land
                $landData['site_id'] = $site->id;
                $landData['governorate'] = $validated['governorate'];
                $landData['region'] = $this->getRegionFromGovernorate($validated['governorate']);

                if ($action === 'update' && $landId) {
                    // Update existing land
                    $land = $site->lands()->find($landId);
                    if ($land) {
                        $land->update($landData);
                    }
                } else {
                    // Create new land
                    $site->lands()->create($landData);
                }
            }

            // Update site total area based on current lands
            $totalLandArea = $site->lands()->sum('area_m2');
            if ($totalLandArea > 0) {
                $site->update(['area_m2' => $totalLandArea]);
            }
        }

        return redirect()->route('sites.index')->with('success', 'Site updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Site $site)
    {
        $site->delete(); // Soft delete
        return redirect()->route('sites.index')->with('success', 'Site deleted successfully!');
    }

    /**
     * Display deleted sites
     */
    public function deleted()
    {
        $sites = Site::onlyTrashed()
            ->with(['buildings', 'lands', 'zoningStatuses'])
            ->latest('deleted_at')
            ->paginate(15);
        return view('sites.deleted', compact('sites'));
    }

    /**
     * Restore a soft deleted site
     */
    public function restore($id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->restore();
        return redirect()->route('sites.deleted')->with('success', 'Site restored successfully!');
    }

    /**
     * Permanently delete a site
     */
    public function forceDestroy($id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->forceDelete();
        return redirect()->route('sites.deleted')->with('success', 'Site permanently deleted!');
    }
}
