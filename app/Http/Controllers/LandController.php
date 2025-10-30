<?php

namespace App\Http\Controllers;

use App\Models\Land;
use App\Models\Site;
use App\Models\ZoningStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:sites_lands_buildings')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'governorate',
            'zoning',
            'directorate',
            'village',
            'basin',
            'neighborhood',
            'plot_number',
            'region',
        ]);

        $query = Land::query()
            ->with([
                'site' => fn($siteQuery) => $siteQuery->withTrashed(),
                'buildings',
            ])
            ->withCount('buildings');

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('plot_key', 'like', "%{$searchTerm}%")
                    ->orWhere('plot_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('site', function ($siteQuery) use ($searchTerm) {
                        $siteQuery->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('code', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (!empty($filters['governorate'])) {
            $query->where('governorate', $filters['governorate']);
        }

        if (!empty($filters['zoning'])) {
            $query->where('zoning', 'like', "%{$filters['zoning']}%");
        }

        if (!empty($filters['directorate'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('directorate', 'like', "%{$filters['directorate']}%")
                    ->orWhere('directorate_number', 'like', "%{$filters['directorate']}%");
            });
        }

        if (!empty($filters['village'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('village', 'like', "%{$filters['village']}%")
                    ->orWhere('village_number', 'like', "%{$filters['village']}%");
            });
        }

        if (!empty($filters['basin'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('basin', 'like', "%{$filters['basin']}%")
                    ->orWhere('basin_number', 'like', "%{$filters['basin']}%");
            });
        }

        if (!empty($filters['neighborhood'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('neighborhood', 'like', "%{$filters['neighborhood']}%")
                    ->orWhere('neighborhood_number', 'like', "%{$filters['neighborhood']}%");
            });
        }

        if (!empty($filters['plot_number'])) {
            $query->where('plot_number', 'like', "%{$filters['plot_number']}%");
        }

        if (!empty($filters['region'])) {
            $query->where('region', 'like', "%{$filters['region']}%");
        }

        $sort = $request->query('sort', 'number');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'plot_key' => 'plot_key',
            'governorate' => 'governorate',
            'area' => 'area_m2',
        ];


        if ($sort === 'number') {
            $query->orderBy('id', $direction === 'asc' ? 'desc' : 'asc');
        } elseif ($sort === 'site') {
            $query->orderBy(
                Site::select('name')
                    ->withTrashed()
                    ->whereColumn('sites.id', 'lands.site_id'),
                $direction
            );
        } elseif ($sort === 'buildings') {
            $query->orderBy('buildings_count', $direction);
        } elseif (array_key_exists($sort, $sortMap)) {
            $query->orderBy($sortMap[$sort], $direction);
        } else {
            $query->orderBy('id', 'desc');
            $sort = 'number';
            $direction = 'asc';
        }

        $lands = $query->paginate(15)->appends($request->query());

        $zoningStatuses = ZoningStatus::orderBy('name_ar')->get();
        $governorates = Land::select('governorate')
            ->whereNotNull('governorate')
            ->distinct()
            ->orderBy('governorate')
            ->pluck('governorate');

        return view('lands.index', [
            'lands' => $lands,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'zoningStatuses' => $zoningStatuses,
            'governorates' => $governorates,
        ]);
    }

    public function deleted()
    {
        $lands = Land::onlyTrashed()
            ->with([
                'site' => fn($query) => $query->withTrashed(),
            ])
            ->latest('deleted_at')
            ->paginate(15);

        return view('lands.deleted', compact('lands'));
    }

    public function create()
    {
        $sites = Site::all();
        $zoningStatuses = ZoningStatus::orderBy('name_ar')->get();
        return view('lands.create', compact('sites', 'zoningStatuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'governorate' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'directorate' => 'required|string|max:255',
            'directorate_number' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'village_number' => 'nullable|string|max:255',
            'basin' => 'required|string|max:255',
            'basin_number' => 'required|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'neighborhood_number' => 'nullable|string|max:255',
            'plot_number' => 'required|string|max:255',
            'plot_key' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'map_location' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ownership_doc' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
            'site_plan' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
            'zoning_plan' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
        ]);

        // Handle zoning statuses - convert array to comma-separated string
        if ($request->has('zoning_statuses') && is_array($request->zoning_statuses)) {
            $zoningNames = \App\Models\ZoningStatus::whereIn('id', $request->zoning_statuses)
                ->pluck('name_ar')
                ->implode(', ');
            $validated['zoning'] = $zoningNames;
        }

        // Handle file uploads
        if ($request->hasFile('ownership_doc')) {
            $validated['ownership_doc'] = $request->file('ownership_doc')->store('lands/ownership_docs', 'private');
        }

        if ($request->hasFile('site_plan')) {
            $validated['site_plan'] = $request->file('site_plan')->store('lands/site_plans', 'private');
        }

        if ($request->hasFile('zoning_plan')) {
            $validated['zoning_plan'] = $request->file('zoning_plan')->store('lands/zoning_plans', 'private');
        }

        Land::create($validated);

        return redirect()->route('lands.index')->with('success', 'Land created successfully!');
    }

    public function show(Land $land)
    {
        $land->load(['site.images', 'buildings.images', 'renovations', 'images']);
        return view('lands.show', compact('land'));
    }

    public function edit(Land $land)
    {
        $sites = Site::all();
        $zoningStatuses = ZoningStatus::orderBy('name_ar')->get();
        return view('lands.edit', compact('land', 'sites', 'zoningStatuses'));
    }

    public function update(Request $request, Land $land)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'governorate' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'directorate' => 'required|string|max:255',
            'directorate_number' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'village_number' => 'nullable|string|max:255',
            'basin' => 'required|string|max:255',
            'basin_number' => 'required|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'neighborhood_number' => 'nullable|string|max:255',
            'plot_number' => 'required|string|max:255',
            'plot_key' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'map_location' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ownership_doc' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
            'site_plan' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
            'zoning_plan' => 'nullable|file|mimes:jpg,jpeg,pdf|max:10240',
        ]);

        // Handle zoning statuses - convert array to comma-separated string
        if ($request->has('zoning_statuses') && is_array($request->zoning_statuses)) {
            $zoningNames = \App\Models\ZoningStatus::whereIn('id', $request->zoning_statuses)
                ->pluck('name_ar')
                ->implode(', ');
            $validated['zoning'] = $zoningNames;
        }

        // Handle file uploads
        if ($request->hasFile('ownership_doc')) {
            $this->deletePath($land->ownership_doc);
            $validated['ownership_doc'] = $request->file('ownership_doc')->store('lands/ownership_docs', 'private');
        }

        if ($request->hasFile('site_plan')) {
            $this->deletePath($land->site_plan);
            $validated['site_plan'] = $request->file('site_plan')->store('lands/site_plans', 'private');
        }

        if ($request->hasFile('zoning_plan')) {
            $this->deletePath($land->zoning_plan);
            $validated['zoning_plan'] = $request->file('zoning_plan')->store('lands/zoning_plans', 'private');
        }

        $land->update($validated);
        return redirect()->route('lands.show', $land)->with('success', 'Land updated successfully!');
    }

    public function document(Land $land, string $document)
    {
        $attributeMap = [
            'ownership' => 'ownership_doc',
            'site-plan' => 'site_plan',
            'zoning-plan' => 'zoning_plan',
        ];

        if (!array_key_exists($document, $attributeMap)) {
            abort(404);
        }

        $attribute = $attributeMap[$document];
        $path = $land->{$attribute};

        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'File not found.');
        }

        $absolutePath = Storage::disk('private')->path($path);
        $downloadName = basename($path);

        if (request()->boolean('download')) {
            return response()->download($absolutePath, $downloadName);
        }

        $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
        ]);
    }

    public function destroy(Land $land)
    {
        $land->delete();
        return redirect()->route('lands.index')->with('success', 'Land moved to trash successfully!');
    }

    public function restore($id)
    {
        $land = Land::onlyTrashed()->findOrFail($id);
        $land->restore();

        return redirect()->route('lands.deleted')->with('success', 'Land restored successfully!');
    }

    public function forceDestroy($id)
    {
        $land = Land::withTrashed()->findOrFail($id);

        $this->deleteLandMedia($land);

        $land->buildings()->detach();
        $land->reInnovations()->withTrashed()->get()->each->forceDelete();

        $land->forceDelete();

        return redirect()->route('lands.deleted')->with('success', 'Land permanently deleted successfully!');
    }

    protected function deleteLandMedia(Land $land): void
    {
        $documentAttributes = ['ownership_doc', 'site_plan', 'zoning_plan'];

        foreach ($documentAttributes as $attribute) {
            $path = $land->{$attribute};
            $this->deletePath($path);
        }

        if ($land->photos) {
            $photos = $land->photos;

            if (is_string($photos)) {
                $decoded = json_decode($photos, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $photos = $decoded;
                }
            }

            $photoPaths = is_array($photos) ? $photos : [$photos];

            foreach ($photoPaths as $photoPath) {
                $this->deletePath($photoPath);
            }
        }
    }

    private function deletePath(?string $path): void
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
                // Ignore disk errors to avoid breaking flow if disk is not configured
            }
        }
    }
}
