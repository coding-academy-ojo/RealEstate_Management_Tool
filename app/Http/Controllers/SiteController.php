<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\ZoningStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        // TEMPORARY DEBUG - Remove after testing
        Log::info('=== RAW REQUEST DEBUG ===');
        Log::info('All input keys: ' . json_encode(array_keys($request->all())));
        Log::info('All file keys: ' . json_encode(array_keys($request->allFiles())));

        if ($request->has('land_images')) {
            $landImages = $request->input('land_images');
            Log::info('land_images input type: ' . gettype($landImages));
            if (is_array($landImages)) {
                Log::info('land_images keys: ' . json_encode(array_keys($landImages)));
            }
        }

        $landImagesFiles = $request->file('land_images');
        if ($landImagesFiles) {
            Log::info('land_images FILE type: ' . gettype($landImagesFiles));
            if (is_array($landImagesFiles)) {
                Log::info('land_images FILE keys: ' . json_encode(array_keys($landImagesFiles)));
                foreach ($landImagesFiles as $idx => $files) {
                    Log::info("  land_images[{$idx}] type: " . gettype($files));
                    if (is_array($files)) {
                        Log::info("  land_images[{$idx}] count: " . count($files));
                    }
                }
            }
        }
        // END DEBUG

        $validated = $request->validate([
            'cluster_no' => 'required|integer|min:1',
            'governorate' => 'required|string|max:3',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'document_names.*' => 'nullable|string|max:255',
            // Lands validation
            'lands' => 'nullable|array',
            'lands.*.zoning_statuses' => 'nullable|array',
            'lands.*.zoning_statuses.*' => 'exists:zoning_statuses,id',
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
            'lands.*.ownership_doc' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'lands.*.site_plan' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'lands.*.zoning_plan' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            // Image uploads
            'site_images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'land_images.*.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
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
                $path = $file->store('sites/other_documents', 'private');
                $otherDocuments[] = [
                    'name' => $documentNames[$index] ?? 'Document ' . ($index + 1),
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }
        $validated['other_documents'] = $otherDocuments;

        $site = Site::create($validated);

        // DEBUG: Check what we're receiving
        Log::info('=== SITE CREATION DEBUG ===');
        Log::info('Site images count: ' . ($request->hasFile('site_images') ? count($request->file('site_images')) : 0));
        Log::info('Request file keys: ' . json_encode(array_keys($request->allFiles())));

        $landImagesFromRequest = $request->file('land_images', []);
        Log::info('Land images received: ' . json_encode(array_keys($landImagesFromRequest)));
        foreach ($landImagesFromRequest as $idx => $imgs) {
            Log::info("  Index {$idx}: " . (is_array($imgs) ? count($imgs) . ' images' : 'not array'));
        }

        // Handle site images
        if ($request->hasFile('site_images')) {
            Log::info('Site images found: ' . count($request->file('site_images')));
            foreach ($request->file('site_images') as $imageFile) {
                $path = $imageFile->store('sites/images', 'private');
                $site->images()->create([
                    'filename' => basename($path),
                    'original_name' => $imageFile->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $imageFile->getMimeType(),
                    'size' => $imageFile->getSize(),
                ]);
            }
        }

        // Debug log for land images
        Log::info('Request has land_images: ' . ($request->has('land_images') ? 'YES' : 'NO'));
        Log::info('All files in request: ' . json_encode(array_keys($request->allFiles())));
        if ($request->has('land_images')) {
            Log::info('Land images structure: ' . json_encode(array_keys($request->input('land_images', []))));
        }

        // Create lands if provided and collect zoning statuses
        $allZoningIds = [];
        if (!empty($validated['lands'])) {
            foreach ($validated['lands'] as $index => $landData) {
                // Extract zoning statuses for this land
                $landZoningIds = $landData['zoning_statuses'] ?? [];
                unset($landData['zoning_statuses']);

                // Handle document uploads for this land
                if ($request->hasFile("lands.{$index}.ownership_doc")) {
                    $landData['ownership_doc'] = $request->file("lands.{$index}.ownership_doc")->store('lands/ownership_docs', 'private');
                }
                if ($request->hasFile("lands.{$index}.site_plan")) {
                    $landData['site_plan'] = $request->file("lands.{$index}.site_plan")->store('lands/site_plans', 'private');
                }
                if ($request->hasFile("lands.{$index}.zoning_plan")) {
                    $landData['zoning_plan'] = $request->file("lands.{$index}.zoning_plan")->store('lands/zoning_plans', 'private');
                }

                // Add site_id and governorate/region to each land
                $landData['site_id'] = $site->id;
                $landData['governorate'] = $validated['governorate'];
                $landData['region'] = $validated['region'];

                // Create the land
                $land = $site->lands()->create($landData);

                // Handle land images for this specific land index
                Log::info("Checking for land images at index {$index}");

                // Check if land_images exists in the request and has this index
                $allLandImages = $request->file('land_images', []);
                Log::info("land_images array keys: " . json_encode(array_keys($allLandImages)));

                if (isset($allLandImages[$index]) && is_array($allLandImages[$index])) {
                    $landImages = $allLandImages[$index];
                    Log::info("Found " . count($landImages) . " images for land index {$index}");

                    foreach ($landImages as $imageFile) {
                        if ($imageFile && $imageFile->isValid()) {
                            $path = $imageFile->store('lands/images', 'private');
                            Log::info("Stored land image at: {$path}");

                            $land->images()->create([
                                'filename' => basename($path),
                                'original_name' => $imageFile->getClientOriginalName(),
                                'path' => $path,
                                'mime_type' => $imageFile->getMimeType(),
                                'size' => $imageFile->getSize(),
                            ]);
                        }
                    }
                } else {
                    Log::info("No images found for land index {$index}");
                }

                // Attach zoning statuses to this land
                if (!empty($landZoningIds)) {
                    $land->zoningStatuses()->attach($landZoningIds);
                    $allZoningIds = array_merge($allZoningIds, $landZoningIds);
                }
            }

            // Update site total area based on lands
            $totalLandArea = collect($validated['lands'])->sum('area_m2');
            if ($totalLandArea > 0) {
                $site->update(['area_m2' => $totalLandArea]);
            }
        }

        // Aggregate all unique zoning statuses from lands and assign to site
        if (!empty($allZoningIds)) {
            $uniqueZoningIds = array_unique($allZoningIds);
            $site->zoningStatuses()->sync($uniqueZoningIds);
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
        $site->load(['buildings.images', 'lands.images', 'renovations', 'zoningStatuses', 'images']);
        return view('sites.show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        // Eager load lands with their zoning statuses
        $site->load('lands.zoningStatuses');
        $zoningStatuses = ZoningStatus::active()->orderBy('name_ar')->get();
        return view('sites.edit', compact('site', 'zoningStatuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Site $site)
    {
        // TEMPORARY DEBUG - Remove after testing
        Log::info('=== UPDATE REQUEST DEBUG ===');
        Log::info('All input keys: ' . json_encode(array_keys($request->all())));
        Log::info('All file keys: ' . json_encode(array_keys($request->allFiles())));

        $landImagesFiles = $request->file('land_images');
        if ($landImagesFiles) {
            Log::info('land_images FILE keys: ' . json_encode(array_keys($landImagesFiles)));
            foreach ($landImagesFiles as $idx => $files) {
                Log::info("  land_images[{$idx}] count: " . (is_array($files) ? count($files) : 'not array'));
            }
        }
        // END DEBUG

        $validated = $request->validate([
            'cluster_no' => 'required|integer|min:1',
            'governorate' => 'required|string|max:3',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'other_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'document_names.*' => 'nullable|string|max:255',
            'remove_documents' => 'nullable|array',
            // Lands validation
            'lands' => 'nullable|array',
            'lands.*.id' => 'nullable|exists:lands,id',
            'lands.*.zoning_statuses' => 'nullable|array',
            'lands.*.zoning_statuses.*' => 'exists:zoning_statuses,id',
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
            'lands.*.ownership_doc' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'lands.*.site_plan' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'lands.*.zoning_plan' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'delete_lands' => 'nullable|array',
            'delete_lands.*' => 'exists:lands,id',
            // Image uploads and removals
            'site_images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'land_images.*.*' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'remove_site_images' => 'nullable|array',
            'remove_site_images.*' => 'nullable|exists:images,id',
            'remove_land_images' => 'nullable|array',
            'remove_land_images.*.*' => 'nullable|exists:images,id',
        ]);

        // Handle other documents
        $currentDocuments = $site->other_documents ?? [];

        // Remove selected documents
        if (!empty($validated['remove_documents'])) {
            foreach ($validated['remove_documents'] as $index) {
                if (isset($currentDocuments[$index]) && isset($currentDocuments[$index]['path'])) {
                    $this->deleteStoredPath($currentDocuments[$index]['path']);
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
                $path = $file->store('sites/other_documents', 'private');
                $currentDocuments[] = [
                    'name' => $documentNames[$index] ?? 'Document ' . ($index + 1),
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }

        $validated['other_documents'] = $currentDocuments;

        $site->update($validated);

        // Handle site image removals
        if (!empty($validated['remove_site_images'])) {
            foreach ($validated['remove_site_images'] as $imageId) {
                if ($imageId) {
                    $image = $site->images()->find($imageId);
                    if ($image) {
                        $this->deleteStoredPath($image->path);
                        $image->delete();
                    }
                }
            }
        }

        // Handle land image removals
        if (!empty($validated['remove_land_images'])) {
            foreach ($validated['remove_land_images'] as $landId => $imageIds) {
                $land = $site->lands()->find($landId);
                if ($land) {
                    foreach ($imageIds as $imageId) {
                        if ($imageId) {
                            $image = $land->images()->find($imageId);
                            if ($image) {
                                $this->deleteStoredPath($image->path);
                                $image->delete();
                            }
                        }
                    }
                }
            }
        }

        // Handle new site images
        if ($request->hasFile('site_images')) {
            foreach ($request->file('site_images') as $imageFile) {
                $path = $imageFile->store('sites/images', 'private');
                $site->images()->create([
                    'filename' => basename($path),
                    'original_name' => $imageFile->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $imageFile->getMimeType(),
                    'size' => $imageFile->getSize(),
                ]);
            }
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

        // Handle lands update/creation and collect all zoning statuses
        if (!empty($validated['lands'])) {
            foreach ($validated['lands'] as $index => $landData) {
                $action = $landData['_action'] ?? 'create';
                $landId = $landData['id'] ?? null;
                $existingLand = null;
                if ($action === 'update' && $landId) {
                    $existingLand = $site->lands()->find($landId);
                }

                // Extract zoning statuses for this land
                $landZoningIds = $landData['zoning_statuses'] ?? [];
                unset($landData['zoning_statuses']);

                // Remove action field from data
                unset($landData['_action']);
                unset($landData['id']);

                // Handle document uploads for this land
                if ($request->hasFile("lands.{$index}.ownership_doc")) {
                    if ($existingLand) {
                        $this->deleteStoredPath($existingLand->ownership_doc);
                    }
                    $landData['ownership_doc'] = $request->file("lands.{$index}.ownership_doc")->store('lands/ownership_docs', 'private');
                }
                if ($request->hasFile("lands.{$index}.site_plan")) {
                    if ($existingLand) {
                        $this->deleteStoredPath($existingLand->site_plan);
                    }
                    $landData['site_plan'] = $request->file("lands.{$index}.site_plan")->store('lands/site_plans', 'private');
                }
                if ($request->hasFile("lands.{$index}.zoning_plan")) {
                    if ($existingLand) {
                        $this->deleteStoredPath($existingLand->zoning_plan);
                    }
                    $landData['zoning_plan'] = $request->file("lands.{$index}.zoning_plan")->store('lands/zoning_plans', 'private');
                }

                // Add site_id and governorate/region to each land
                $landData['site_id'] = $site->id;
                $landData['governorate'] = $validated['governorate'];
                $landData['region'] = $this->getRegionFromGovernorate($validated['governorate']);

                if ($action === 'update' && $existingLand) {
                    // Update existing land
                    $existingLand->update($landData);
                    // Sync zoning statuses for this land
                    $existingLand->zoningStatuses()->sync($landZoningIds);

                    // Handle new images for existing land using land ID
                    Log::info("Checking for images for existing land ID {$existingLand->id}");
                    $allLandImages = $request->file('land_images', []);

                    if (isset($allLandImages[$existingLand->id]) && is_array($allLandImages[$existingLand->id])) {
                        $landImages = $allLandImages[$existingLand->id];
                        Log::info("Found " . count($landImages) . " images for existing land {$existingLand->id}");

                        foreach ($landImages as $imageFile) {
                            if ($imageFile && $imageFile->isValid()) {
                                $path = $imageFile->store('lands/images', 'private');
                                Log::info("Stored land image at: {$path}");

                                $existingLand->images()->create([
                                    'filename' => basename($path),
                                    'original_name' => $imageFile->getClientOriginalName(),
                                    'path' => $path,
                                    'mime_type' => $imageFile->getMimeType(),
                                    'size' => $imageFile->getSize(),
                                ]);
                            }
                        }
                    } else {
                        Log::info("No new images for existing land {$existingLand->id}");
                    }
                } else {
                    // Create new land
                    $land = $site->lands()->create($landData);
                    // Attach zoning statuses to new land
                    if (!empty($landZoningIds)) {
                        $land->zoningStatuses()->attach($landZoningIds);
                    }

                    // For new lands created during edit, we need to use a temporary ID approach
                    // This is a limitation - recommend adding lands first, then images
                    Log::info("New land created during edit - images should be added separately");
                }
            }

            // Update site total area based on current lands
            $totalLandArea = $site->lands()->sum('area_m2');
            if ($totalLandArea > 0) {
                $site->update(['area_m2' => $totalLandArea]);
            }
        }

        // Aggregate all unique zoning statuses from all site's lands and sync to site
        // Reload lands to ensure we have the latest data including newly created ones
        $site->load('lands.zoningStatuses');

        $allLandZoningIds = [];
        foreach ($site->lands as $land) {
            $landZonings = $land->zoningStatuses->pluck('id')->toArray();
            $allLandZoningIds = array_merge($allLandZoningIds, $landZonings);
        }
        $uniqueZoningIds = array_unique($allLandZoningIds);
        $site->zoningStatuses()->sync($uniqueZoningIds);

        return redirect()->route('sites.index')->with('success', 'Site updated successfully!');
    }

    public function document(Site $site, int $document)
    {
        $documents = $site->other_documents ?? [];

        if (!isset($documents[$document])) {
            abort(404);
        }

        $entry = $documents[$document];
        $path = $entry['path'] ?? null;

        if (!$path) {
            abort(404, 'File not found.');
        }

        $disk = $this->resolveDiskForPath($path);

        if (!$disk || !Storage::disk($disk)->exists($path)) {
            abort(404, 'File not found.');
        }

        $absolutePath = Storage::disk($disk)->path($path);
        $downloadName = $entry['original_name'] ?? basename($path);

        if (request()->boolean('download')) {
            return response()->download($absolutePath, $downloadName);
        }

        $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
        ]);
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

        $documents = $site->other_documents ?? [];
        foreach ($documents as $document) {
            if (!empty($document['path'])) {
                $this->deleteStoredPath($document['path']);
            }
        }

        $site->forceDelete();
        return redirect()->route('sites.deleted')->with('success', 'Site permanently deleted!');
    }

    private function deleteStoredPath(?string $path): void
    {
        if (!$path) {
            return;
        }

        foreach (['private', 'public'] as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    Storage::disk($disk)->delete($path);
                }
            } catch (\Throwable $exception) {
                // Ignore disk errors to avoid interrupting user flow
            }
        }
    }

    private function resolveDiskForPath(string $path): ?string
    {
        foreach (['private', 'public'] as $disk) {
            try {
                if (Storage::disk($disk)->exists($path)) {
                    return $disk;
                }
            } catch (\Throwable $exception) {
                // Ignore disk errors; try next disk
            }
        }

        return null;
    }
}
