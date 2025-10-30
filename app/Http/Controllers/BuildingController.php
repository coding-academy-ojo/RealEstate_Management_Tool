<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ElectricityService;
use App\Models\Renovation;
use App\Models\Site;
use App\Models\WaterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BuildingController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:sites_lands_buildings')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'permit', 'area', 'property_type']);

        $query = Building::query()
            ->with([
                'site' => fn($siteQuery) => $siteQuery->withTrashed(),
                'waterServices',
                'electricityServices',
            ])
            ->withCount(['waterServices', 'electricityServices']);

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($builder) use ($searchTerm) {
                $builder->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('site', function ($siteQuery) use ($searchTerm) {
                        $siteQuery->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if (!empty($filters['permit'])) {
            switch ($filters['permit']) {
                case 'building':
                    $query->where('has_building_permit', true);
                    break;
                case 'occupancy':
                    $query->where('has_occupancy_permit', true);
                    break;
                case 'profession':
                    $query->where('has_profession_permit', true);
                    break;
                case 'no-permits':
                    $query->where(function ($inner) {
                        $inner->where('has_building_permit', false)
                            ->where('has_occupancy_permit', false)
                            ->where('has_profession_permit', false);
                    });
                    break;
            }
        }

        if (!empty($filters['area'])) {
            switch ($filters['area']) {
                case '0-500':
                    $query->whereBetween('area_m2', [0, 500]);
                    break;
                case '500-1000':
                    $query->where('area_m2', '>', 500)->where('area_m2', '<=', 1000);
                    break;
                case '1000-2000':
                    $query->where('area_m2', '>', 1000)->where('area_m2', '<=', 2000);
                    break;
                case '2000+':
                    $query->where('area_m2', '>', 2000);
                    break;
            }
        }

        if (!empty($filters['property_type']) && in_array($filters['property_type'], ['owned', 'rental'], true)) {
            $query->where('property_type', $filters['property_type']);
        }

        $sort = $request->query('sort', 'number');
        $direction = strtolower($request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $sortMap = [
            'code' => 'code',
            'name' => 'name',
            'area' => 'area_m2',
            'property_type' => 'property_type',
        ];

        if ($sort === 'number') {
            $query->orderBy('id', $direction === 'asc' ? 'desc' : 'asc');
        } elseif ($sort === 'site') {
            $query->orderBy(
                Site::select('name')->whereColumn('sites.id', 'buildings.site_id'),
                $direction
            );
        } elseif ($sort === 'services') {
            $query->orderByRaw('(COALESCE(water_services_count, 0) + COALESCE(electricity_services_count, 0)) ' . $direction);
        } elseif (array_key_exists($sort, $sortMap)) {
            $query->orderBy($sortMap[$sort], $direction);
        } else {
            $query->orderBy('id', 'desc');
            $sort = 'number';
            $direction = 'asc';
        }

        $buildings = $query->paginate(15)->appends($request->query());

        return view('buildings.index', [
            'buildings' => $buildings,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
    public function create()
    {
        $sites = Site::all();
        return view('buildings.create', compact('sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'lands' => 'sometimes|array',
            'lands.*' => 'exists:lands,id',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'property_type' => ['required', Rule::in(['owned', 'rental'])],
            'contract_start_date' => ['nullable', 'date', 'required_if:property_type,rental'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date', 'required_if:property_type,rental'],
            'contract_value' => ['nullable', 'numeric', 'min:0', 'required_if:property_type,rental'],
            'special_conditions' => ['nullable', 'string'],
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'annual_increase_rate' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:property_type,rental'],
            'increase_effective_date' => ['nullable', 'date', 'after_or_equal:contract_start_date', 'required_if:property_type,rental'],
            'has_building_permit' => 'nullable|boolean',
            'has_occupancy_permit' => 'nullable|boolean',
            'has_profession_permit' => 'nullable|boolean',
            'building_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'occupancy_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'profession_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'as_built_drawing_pdf' => 'nullable|file|mimes:pdf|max:51200',
            'as_built_drawing_cad' => 'nullable|file|mimes:dwg,dxf|max:51200',
            'remarks' => 'nullable|string',
        ]);

        $propertyType = $validated['property_type'];

        $building = Building::create([
            'site_id' => $validated['site_id'],
            'name' => $validated['name'],
            'area_m2' => $validated['area_m2'],
            'property_type' => $propertyType,
            'contract_start_date' => $propertyType === 'rental' ? $validated['contract_start_date'] : null,
            'contract_end_date' => $propertyType === 'rental' ? $validated['contract_end_date'] : null,
            'contract_value' => $propertyType === 'rental' ? $validated['contract_value'] : null,
            'special_conditions' => $propertyType === 'rental' ? ($validated['special_conditions'] ?? null) : null,
            'annual_increase_rate' => $propertyType === 'rental' ? $validated['annual_increase_rate'] : null,
            'increase_effective_date' => $propertyType === 'rental' ? $validated['increase_effective_date'] : null,
            'has_building_permit' => $request->has('has_building_permit'),
            'has_occupancy_permit' => $request->has('has_occupancy_permit'),
            'has_profession_permit' => $request->has('has_profession_permit'),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        // Handle file uploads
        if ($request->hasFile('building_permit_file')) {
            $path = $request->file('building_permit_file')->store('permits/buildings', 'private');
            $building->building_permit_file = $path;
        }

        if ($request->hasFile('occupancy_permit_file')) {
            $path = $request->file('occupancy_permit_file')->store('permits/occupancy', 'private');
            $building->occupancy_permit_file = $path;
        }

        if ($request->hasFile('profession_permit_file')) {
            $path = $request->file('profession_permit_file')->store('permits/profession', 'private');
            $building->profession_permit_file = $path;
        }

        if ($request->hasFile('as_built_drawing_pdf')) {
            $building->as_built_drawing_pdf = $request->file('as_built_drawing_pdf')->store('drawings/as-built/pdf', 'private');
        }

        if ($request->hasFile('as_built_drawing_cad')) {
            $building->as_built_drawing_cad = $request->file('as_built_drawing_cad')->store('drawings/as-built/cad', 'private');
        }

        if ($propertyType === 'rental' && $request->hasFile('contract_file')) {
            $building->contract_file = $request->file('contract_file')->store('contracts/buildings', 'private');
        }

        $building->save();

        // Attach selected lands to the building (if any)
        if ($request->has('lands') && is_array($request->lands)) {
            $building->lands()->attach($request->lands);
        }

        return redirect()->route('buildings.index')->with('success', 'Building created successfully!');
    }

    public function show(Building $building)
    {
        $building->load([
            'site.images',
            'lands.images',
            'lands.zoningStatuses',
            'waterServices' => fn($query) => $query->with('latestReading'),
            'electricityServices',
            'renovations',
            'images',
        ]);
        $documents = collect([
            'building-permit' => [
                'label' => 'Building Permit',
                'attribute' => 'building_permit_file',
                'status' => (bool) $building->has_building_permit,
                'is_permit' => true,
            ],
            'occupancy-permit' => [
                'label' => 'Occupancy Permit',
                'attribute' => 'occupancy_permit_file',
                'status' => (bool) $building->has_occupancy_permit,
                'is_permit' => true,
            ],
            'profession-permit' => [
                'label' => 'Profession Permit',
                'attribute' => 'profession_permit_file',
                'status' => (bool) $building->has_profession_permit,
                'is_permit' => true,
            ],
            'as-built-drawing-pdf' => [
                'label' => 'As-Built Drawing (PDF)',
                'attribute' => 'as_built_drawing_pdf',
                'status' => null,
                'is_permit' => false,
            ],
            'as-built-drawing-cad' => [
                'label' => 'As-Built Drawing (CAD)',
                'attribute' => 'as_built_drawing_cad',
                'status' => null,
                'is_permit' => false,
            ],
            'contract-document' => [
                'label' => 'Contract Document',
                'attribute' => 'contract_file',
                'status' => $building->property_type === 'rental',
                'is_permit' => false,
            ],
        ])->map(function (array $config, string $slug) use ($building) {
            $path = $building->{$config['attribute']} ?? null;
            $disk = $this->resolveDiskForPath($path);
            $exists = (bool) $disk;
            $extension = $exists ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : null;
            $inlineUrl = $exists ? route('buildings.files.show', [$building, $slug]) : null;

            return [
                'slug' => $slug,
                'label' => $config['label'],
                'is_permit' => $config['is_permit'],
                'status_boolean' => is_null($config['status']) ? null : (bool) $config['status'],
                'status_text' => is_null($config['status'])
                    ? ($exists ? 'Uploaded' : 'Not uploaded')
                    : ((bool) $config['status'] ? 'Yes' : 'No'),
                'path' => $path,
                'has_file' => $exists,
                'file_name' => $exists ? basename($path) : null,
                'extension' => $extension,
                'inline_url' => $inlineUrl,
                'absolute_url' => $exists ? route('buildings.files.show', [$building, $slug], true) : null,
                'download_url' => $exists ? $inlineUrl . '?download=1' : null,
            ];
        });

        return view('buildings.show', [
            'building' => $building,
            'documents' => $documents,
        ]);
    }

    public function edit(Building $building)
    {
        $building->load(['site', 'lands']);
        $sites = Site::all();

        return view('buildings.edit', [
            'building' => $building,
            'sites' => $sites,
            'selectedLandIds' => $building->lands->pluck('id')->toArray(),
        ]);
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'lands' => 'sometimes|array',
            'lands.*' => 'exists:lands,id',
            'name' => 'required|string|max:255',
            'area_m2' => 'required|numeric|min:0',
            'property_type' => ['required', Rule::in(['owned', 'rental'])],
            'contract_start_date' => ['nullable', 'date', 'required_if:property_type,rental'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:contract_start_date', 'required_if:property_type,rental'],
            'contract_value' => ['nullable', 'numeric', 'min:0', 'required_if:property_type,rental'],
            'special_conditions' => ['nullable', 'string'],
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'annual_increase_rate' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:property_type,rental'],
            'increase_effective_date' => ['nullable', 'date', 'after_or_equal:contract_start_date', 'required_if:property_type,rental'],
            'has_building_permit' => 'nullable|boolean',
            'has_occupancy_permit' => 'nullable|boolean',
            'has_profession_permit' => 'nullable|boolean',
            'building_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'occupancy_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'profession_permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'as_built_drawing_pdf' => 'nullable|file|mimes:pdf|max:51200',
            'as_built_drawing_cad' => 'nullable|file|mimes:dwg,dxf|max:51200',
            'remarks' => 'nullable|string',
        ]);

        $propertyType = $validated['property_type'];

        $building->site_id = $validated['site_id'];
        $building->name = $validated['name'];
        $building->area_m2 = $validated['area_m2'];
        $building->property_type = $propertyType;
        if ($propertyType === 'rental') {
            $building->contract_start_date = $validated['contract_start_date'];
            $building->contract_end_date = $validated['contract_end_date'];
            $building->contract_value = $validated['contract_value'];
            $building->special_conditions = $validated['special_conditions'] ?? null;
            $building->annual_increase_rate = $validated['annual_increase_rate'];
            $building->increase_effective_date = $validated['increase_effective_date'];
        } else {
            $building->contract_start_date = null;
            $building->contract_end_date = null;
            $building->contract_value = null;
            $building->special_conditions = null;
            $building->annual_increase_rate = null;
            $building->increase_effective_date = null;
            if ($building->contract_file) {
                $this->deleteFileIfExists($building->contract_file);
                $building->contract_file = null;
            }
        }
        $building->has_building_permit = $request->boolean('has_building_permit');
        $building->has_occupancy_permit = $request->boolean('has_occupancy_permit');
        $building->has_profession_permit = $request->boolean('has_profession_permit');
        $building->remarks = $validated['remarks'] ?? null;

        // Handle building permit file
        if ($request->hasFile('building_permit_file')) {
            $this->deleteFileIfExists($building->building_permit_file);
            $building->building_permit_file = $request->file('building_permit_file')->store('permits/buildings', 'private');
        } elseif (!$request->boolean('has_building_permit')) {
            // If checkbox is unchecked, clear the file
            $this->deleteFileIfExists($building->building_permit_file);
            $building->building_permit_file = null;
        }

        // Handle occupancy permit file
        if ($request->hasFile('occupancy_permit_file')) {
            $this->deleteFileIfExists($building->occupancy_permit_file);
            $building->occupancy_permit_file = $request->file('occupancy_permit_file')->store('permits/occupancy', 'private');
        } elseif (!$request->boolean('has_occupancy_permit')) {
            // If checkbox is unchecked, clear the file
            $this->deleteFileIfExists($building->occupancy_permit_file);
            $building->occupancy_permit_file = null;
        }

        // Handle profession permit file
        if ($request->hasFile('profession_permit_file')) {
            $this->deleteFileIfExists($building->profession_permit_file);
            $building->profession_permit_file = $request->file('profession_permit_file')->store('permits/profession', 'private');
        } elseif (!$request->boolean('has_profession_permit')) {
            // If checkbox is unchecked, clear the file
            $this->deleteFileIfExists($building->profession_permit_file);
            $building->profession_permit_file = null;
        }

        if ($request->hasFile('as_built_drawing_pdf')) {
            $this->deleteFileIfExists($building->as_built_drawing_pdf);
            $building->as_built_drawing_pdf = $request->file('as_built_drawing_pdf')->store('drawings/as-built/pdf', 'private');
        }

        if ($request->hasFile('as_built_drawing_cad')) {
            $this->deleteFileIfExists($building->as_built_drawing_cad);
            $building->as_built_drawing_cad = $request->file('as_built_drawing_cad')->store('drawings/as-built/cad', 'private');
        }

        if ($propertyType === 'rental' && $request->hasFile('contract_file')) {
            $this->deleteFileIfExists($building->contract_file);
            $building->contract_file = $request->file('contract_file')->store('contracts/buildings', 'private');
        }

        $building->save();

        $landIds = $request->input('lands', []);
        if (!is_array($landIds)) {
            $landIds = [];
        }
        $building->lands()->sync($landIds);

        return redirect()->route('buildings.index')->with('success', 'Building updated successfully!');
    }

    public function destroy(Building $building)
    {
        $building->waterServices()->get()->each->delete();
        $building->electricityServices()->get()->each->delete();
        $building->renovations()->get()->each->delete();

        $building->delete();

        return redirect()->route('buildings.index')->with('success', 'Building moved to trash successfully!');
    }

    public function file(Building $building, string $document)
    {
        $documentMap = [
            'building-permit' => 'building_permit_file',
            'occupancy-permit' => 'occupancy_permit_file',
            'profession-permit' => 'profession_permit_file',
            'as-built-drawing-pdf' => 'as_built_drawing_pdf',
            'as-built-drawing-cad' => 'as_built_drawing_cad',
            'contract-document' => 'contract_file',
        ];

        if (!array_key_exists($document, $documentMap)) {
            abort(404);
        }

        $attribute = $documentMap[$document];
        $path = $building->{$attribute};
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

    public function deleted()
    {
        $buildings = Building::onlyTrashed()
            ->with([
                'site' => fn($query) => $query->withTrashed(),
                'lands' => fn($query) => $query->withTrashed(),
            ])
            ->withCount(['waterServices', 'electricityServices'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('buildings.deleted', compact('buildings'));
    }

    public function restore($id)
    {
        $building = Building::onlyTrashed()
            ->with([
                'waterServices' => fn($query) => $query->withTrashed(),
                'electricityServices' => fn($query) => $query->withTrashed(),
            'renovations' => fn($query) => $query->withTrashed(),
            ])
            ->findOrFail($id);

        $building->restore();
        $building->waterServices()->withTrashed()->restore();
        $building->electricityServices()->withTrashed()->restore();
        $building->renovations()->withTrashed()->restore();

        return redirect()->route('buildings.deleted')->with('success', 'Building restored successfully!');
    }

    public function forceDestroy($id)
    {
        $building = Building::onlyTrashed()
            ->with([
                'lands' => fn($query) => $query->withTrashed(),
                'waterServices' => fn($query) => $query->withTrashed(),
                'electricityServices' => fn($query) => $query->withTrashed(),
            'renovations' => fn($query) => $query->withTrashed(),
            ])
            ->findOrFail($id);

        foreach (['building_permit_file', 'occupancy_permit_file', 'profession_permit_file', 'as_built_drawing_pdf', 'as_built_drawing_cad', 'contract_file'] as $fileAttribute) {
            $this->deleteFileIfExists($building->{$fileAttribute});
        }

        $building->waterServices()->withTrashed()->get()->each(function (WaterService $service) {
            $this->purgeWaterServiceMedia($service);
            $service->forceDelete();
        });

        $building->electricityServices()->withTrashed()->get()->each(function (ElectricityService $service) {
            $this->purgeElectricityServiceMedia($service);
            $service->forceDelete();
        });

        $building->renovations()->withTrashed()->get()->each(function (Renovation $innovation) {
            $innovation->forceDelete();
        });

        $building->lands()->detach();

        $building->forceDelete();

        return redirect()->route('buildings.deleted')->with('success', 'Building permanently deleted!');
    }

    protected function deleteFileIfExists(?string $path): void
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

    protected function purgeWaterServiceMedia(WaterService $service): void
    {
        $service->loadMissing('readings');

        $this->deleteFileIfExists($service->initial_meter_image);

        foreach ($service->readings as $reading) {
            $this->deleteFileIfExists($reading->meter_image);
            $this->deleteFileIfExists($reading->bill_image);
        }
    }

    protected function purgeElectricityServiceMedia(ElectricityService $service): void
    {
        $this->deleteFileIfExists($service->reset_file);
    }

    protected function resolveDiskForPath(?string $path): ?string
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
