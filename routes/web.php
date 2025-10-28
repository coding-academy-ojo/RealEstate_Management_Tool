<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    // Get all governorate codes
    $governorates = ['AM', 'IR', 'AJ', 'JA', 'MA', 'BA', 'ZA', 'KA', 'TF', 'MN', 'AQ', 'MF'];

    // Prepare map data
    $mapData = [];
    foreach ($governorates as $gov) {
        $sites = \App\Models\Site::where('governorate', $gov)->get();
        $siteIds = $sites->pluck('id');

        $mapData[$gov] = [
            'sites' => $sites->count(),
            'lands' => \App\Models\Land::whereIn('site_id', $siteIds)->count(),
            'buildings' => \App\Models\Building::whereIn('site_id', $siteIds)->count(),
        ];
    }

    $stats = [
        'total_sites' => \App\Models\Site::count(),
        'total_buildings' => \App\Models\Building::count(),
        'total_lands' => \App\Models\Land::count(),
        'total_innovations' => \App\Models\Rennovation::count(),
        'total_water_services' => \App\Models\WaterService::count(),
        'total_electricity_services' => \App\Models\ElectricityService::count(),

        // Buildings by governorate - converted to full English names
        'buildings_by_governorate' => \App\Models\Site::selectRaw('governorate, COUNT(DISTINCT buildings.id) as count')
            ->leftJoin('buildings', 'sites.id', '=', 'buildings.site_id')
            ->groupBy('governorate')
            ->get()
            ->mapWithKeys(function ($item) {
                $site = new \App\Models\Site(['governorate' => $item->governorate]);
                return [$site->governorate_name_en => $item->count];
            }),

        // Sites by governorate - converted to full English names
        'sites_by_governorate' => \App\Models\Site::selectRaw('governorate, COUNT(*) as count')
            ->groupBy('governorate')
            ->get()
            ->mapWithKeys(function ($item) {
                $site = new \App\Models\Site(['governorate' => $item->governorate]);
                return [$site->governorate_name_en => $item->count];
            }),

        // Recent sites
        'recent_sites' => \App\Models\Site::latest()->take(5)->get(),

        // Recent buildings
        'recent_buildings' => \App\Models\Building::with('site')->latest()->take(5)->get(),

        // Recent Activity - All entities with action tracking
        'recent_activities' => collect()
            // Sites activities
            ->merge(\App\Models\Site::latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'site',
                'action' => 'created',
                'icon' => 'geo-alt-fill',
                'color' => 'success',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => $item->governorate_name_en,
                'route' => route('sites.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\Site::latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'site',
                'action' => 'updated',
                'icon' => 'geo-alt-fill',
                'color' => 'primary',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => $item->governorate_name_en,
                'route' => route('sites.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Buildings activities
            ->merge(\App\Models\Building::with('site')->latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'building',
                'action' => 'created',
                'icon' => 'building-fill',
                'color' => 'success',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => $item->site->name ?? 'N/A',
                'route' => route('buildings.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\Building::with('site')->latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'building',
                'action' => 'updated',
                'icon' => 'building-fill',
                'color' => 'primary',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => $item->site->name ?? 'N/A',
                'route' => route('buildings.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Lands activities
            ->merge(\App\Models\Land::with('site')->latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'land',
                'action' => 'created',
                'icon' => 'map-fill',
                'color' => 'success',
                'title' => 'Plot ' . $item->plot_number,
                'subtitle' => 'Basin ' . $item->basin,
                'description' => $item->site->name ?? 'N/A',
                'route' => route('lands.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\Land::with('site')->latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'land',
                'action' => 'updated',
                'icon' => 'map-fill',
                'color' => 'primary',
                'title' => 'Plot ' . $item->plot_number,
                'subtitle' => 'Basin ' . $item->basin,
                'description' => $item->site->name ?? 'N/A',
                'route' => route('lands.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Water Services activities
            ->merge(\App\Models\WaterService::with('building')->latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'water',
                'action' => 'created',
                'icon' => 'droplet-fill',
                'color' => 'success',
                'title' => 'Water Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => $item->building->name ?? 'N/A',
                'route' => route('water-services.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\WaterService::with('building')->latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'water',
                'action' => 'updated',
                'icon' => 'droplet-fill',
                'color' => 'primary',
                'title' => 'Water Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => $item->building->name ?? 'N/A',
                'route' => route('water-services.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Electricity Services activities
            ->merge(\App\Models\ElectricityService::with('building')->latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'electricity',
                'action' => 'created',
                'icon' => 'lightning-charge-fill',
                'color' => 'success',
                'title' => 'Electricity Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => $item->building->name ?? 'N/A',
                'route' => route('electricity-services.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\ElectricityService::with('building')->latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'electricity',
                'action' => 'updated',
                'icon' => 'lightning-charge-fill',
                'color' => 'primary',
                'title' => 'Electricity Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => $item->building->name ?? 'N/A',
                'route' => route('electricity-services.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Rennovations activities
            ->merge(\App\Models\Rennovation::with('innovatable')->latest('created_at')->take(2)->get()->map(fn($item) => [
                'type' => 'innovation',
                'action' => 'created',
                'icon' => 'lightbulb-fill',
                'color' => 'success',
                'title' => $item->name,
                'subtitle' => number_format($item->cost, 2) . ' JOD',
                'description' => class_basename($item->innovatable_type) . ': ' . ($item->innovatable->name ?? 'N/A'),
            'route' => route('rennovations.show', $item),
                'timestamp' => $item->created_at,
            ]))
            ->merge(\App\Models\Rennovation::with('innovatable')->latest('updated_at')->where('updated_at', '>', DB::raw('created_at'))->take(2)->get()->map(fn($item) => [
                'type' => 'innovation',
                'action' => 'updated',
                'icon' => 'lightbulb-fill',
                'color' => 'primary',
                'title' => $item->name,
                'subtitle' => number_format($item->cost, 2) . ' JOD',
                'description' => class_basename($item->innovatable_type) . ': ' . ($item->innovatable->name ?? 'N/A'),
            'route' => route('rennovations.show', $item),
                'timestamp' => $item->updated_at,
            ]))
            // Deleted items (soft deletes)
            ->merge(\App\Models\Site::onlyTrashed()->latest('deleted_at')->take(2)->get()->map(fn($item) => [
                'type' => 'site',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => $item->governorate_name_en,
                'route' => route('sites.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->merge(\App\Models\Building::onlyTrashed()->latest('deleted_at')->take(2)->get()->map(fn($item) => [
                'type' => 'building',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => $item->name,
                'subtitle' => $item->code,
                'description' => 'Deleted',
                'route' => route('buildings.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->merge(\App\Models\Land::onlyTrashed()->latest('deleted_at')->take(2)->get()->map(fn($item) => [
                'type' => 'land',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => 'Plot ' . $item->plot_number,
                'subtitle' => 'Basin ' . $item->basin,
                'description' => 'Deleted',
                'route' => route('lands.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->merge(\App\Models\WaterService::onlyTrashed()->latest('deleted_at')->take(1)->get()->map(fn($item) => [
                'type' => 'water',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => 'Water Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => 'Deleted',
                'route' => route('water-services.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->merge(\App\Models\ElectricityService::onlyTrashed()->latest('deleted_at')->take(1)->get()->map(fn($item) => [
                'type' => 'electricity',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => 'Electricity Service',
                'subtitle' => 'Reg# ' . $item->registration_number,
                'description' => 'Deleted',
                'route' => route('electricity-services.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->merge(\App\Models\Rennovation::onlyTrashed()->latest('deleted_at')->take(1)->get()->map(fn($item) => [
                'type' => 'innovation',
                'action' => 'deleted',
                'icon' => 'trash-fill',
                'color' => 'danger',
                'title' => $item->name,
                'subtitle' => number_format($item->cost, 2) . ' JOD',
                'description' => 'Deleted',
            'route' => route('rennovations.deleted'),
                'timestamp' => $item->deleted_at,
            ]))
            ->sortByDesc('timestamp')
            ->take(10)
            ->values(),

        // Innovation costs
        'total_innovation_cost' => \App\Models\Rennovation::sum('cost'),

        // Buildings with permits
        'buildings_with_permit' => \App\Models\Building::where('has_building_permit', true)->count(),
        'buildings_without_permit' => \App\Models\Building::where('has_building_permit', false)->count(),
    ];

    return view('dashboard', compact('stats', 'mapData'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->middleware('super-admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class)->except(['show']);
    });

    // API routes for dynamic data
    Route::get('/api/sites/{site}/lands', function (\App\Models\Site $site) {
        return response()->json($site->lands);
    });

    Route::get('/api/sites/next-cluster/{governorate}', [\App\Http\Controllers\SiteController::class, 'getNextCluster']);

    Route::get('/api/sites-list', function () {
        return response()->json(\App\Models\Site::select('id', 'code', 'name')->get());
    });

    Route::get('/api/buildings-list', function () {
        return response()->json(\App\Models\Building::with('site:id,code,name')->select('id', 'code', 'name', 'site_id')->get());
    });

    Route::get('/api/lands-list', function () {
        return response()->json(\App\Models\Land::with('site:id,code,name')->select('id', 'plot_number', 'site_id')->get());
    });

    // Sites with soft delete routes
    Route::get('sites/deleted/list', [\App\Http\Controllers\SiteController::class, 'deleted'])->name('sites.deleted');
    Route::post('sites/{id}/restore', [\App\Http\Controllers\SiteController::class, 'restore'])->name('sites.restore');
    Route::delete('sites/{id}/force-delete', [\App\Http\Controllers\SiteController::class, 'forceDestroy'])->name('sites.force-delete');

    // Lands with soft delete routes
    Route::get('lands/deleted/list', [\App\Http\Controllers\LandController::class, 'deleted'])->name('lands.deleted');
    Route::post('lands/{id}/restore', [\App\Http\Controllers\LandController::class, 'restore'])->name('lands.restore');
    Route::delete('lands/{id}/force-delete', [\App\Http\Controllers\LandController::class, 'forceDestroy'])->name('lands.force-delete');

    // Buildings with soft delete routes
    Route::get('buildings/deleted/list', [\App\Http\Controllers\BuildingController::class, 'deleted'])->name('buildings.deleted');
    Route::post('buildings/{id}/restore', [\App\Http\Controllers\BuildingController::class, 'restore'])->name('buildings.restore');
    Route::delete('buildings/{id}/force-delete', [\App\Http\Controllers\BuildingController::class, 'forceDestroy'])->name('buildings.force-delete');

    // Resource routes
    Route::get('buildings/{building}/files/{document}', [\App\Http\Controllers\BuildingController::class, 'file'])
        ->name('buildings.files.show');

    Route::get('water-services/deleted/list', [\App\Http\Controllers\WaterServiceController::class, 'deleted'])
        ->middleware('privilege:water')
        ->name('water-services.deleted');
    Route::post('water-services/{id}/restore', [\App\Http\Controllers\WaterServiceController::class, 'restore'])
        ->middleware('privilege:water')
        ->name('water-services.restore');
    Route::delete('water-services/{id}/force-delete', [\App\Http\Controllers\WaterServiceController::class, 'forceDelete'])
        ->middleware('privilege:water')
        ->name('water-services.force-delete');
    Route::get('water-services/{waterService}/readings/{waterReading}/files/{document}', [\App\Http\Controllers\WaterReadingController::class, 'file'])
        ->middleware('privilege:water')
        ->name('water-services.readings.files.show');
    Route::post('water-services/{waterService}/readings', [\App\Http\Controllers\WaterReadingController::class, 'store'])
        ->middleware('privilege:water')
        ->name('water-services.readings.store');
    Route::put('water-services/{waterService}/readings/{waterReading}', [\App\Http\Controllers\WaterReadingController::class, 'update'])
        ->middleware('privilege:water')
        ->name('water-services.readings.update');
    Route::delete('water-services/{waterService}/readings/{waterReading}', [\App\Http\Controllers\WaterReadingController::class, 'destroy'])
        ->middleware('privilege:water')
        ->name('water-services.readings.destroy');
    Route::resource('water-services', \App\Http\Controllers\WaterServiceController::class);

    Route::get('electricity-services/{electricityService}/files/{document}', [\App\Http\Controllers\ElectricityServiceController::class, 'file'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.files.show');
    Route::get('electricity-services/deleted/list', [\App\Http\Controllers\ElectricityServiceController::class, 'deleted'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.deleted');
    Route::post('electricity-services/{id}/restore', [\App\Http\Controllers\ElectricityServiceController::class, 'restore'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.restore');
    Route::delete('electricity-services/{id}/force-delete', [\App\Http\Controllers\ElectricityServiceController::class, 'forceDelete'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.force-delete');
    Route::resource('electricity-services', \App\Http\Controllers\ElectricityServiceController::class);

    Route::get('rennovations/deleted/list', [\App\Http\Controllers\RennovationController::class, 'deleted'])
        ->middleware('privilege:rennovation')
        ->name('rennovations.deleted');
    Route::post('rennovations/{id}/restore', [\App\Http\Controllers\RennovationController::class, 'restore'])
        ->middleware('privilege:rennovation')
        ->name('rennovations.restore');
    Route::delete('rennovations/{id}/force-delete', [\App\Http\Controllers\RennovationController::class, 'forceDelete'])
        ->middleware('privilege:rennovation')
        ->name('rennovations.force-delete');
    Route::resource('rennovations', \App\Http\Controllers\RennovationController::class);

    // Activities route
    Route::get('activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');

    // Image routes
    Route::post('images/{type}/{id}/upload', [\App\Http\Controllers\ImageController::class, 'upload'])->name('images.upload');
    Route::patch('images/{image}', [\App\Http\Controllers\ImageController::class, 'update'])->name('images.update');
    Route::post('images/{image}/set-primary', [\App\Http\Controllers\ImageController::class, 'setPrimary'])->name('images.set-primary');
    Route::delete('images/{image}', [\App\Http\Controllers\ImageController::class, 'destroy'])->name('images.destroy');
    Route::post('images/{type}/{id}/reorder', [\App\Http\Controllers\ImageController::class, 'reorder'])->name('images.reorder');
    Route::get('images/{image}/show', [\App\Http\Controllers\ImageController::class, 'show'])->name('images.show');

    Route::resource('sites', \App\Http\Controllers\SiteController::class);
    Route::resource('buildings', \App\Http\Controllers\BuildingController::class);
    Route::resource('lands', \App\Http\Controllers\LandController::class);

    // Zoning Status routes
    Route::post('/zoning-statuses', [\App\Http\Controllers\ZoningStatusController::class, 'store'])->name('zoning-statuses.store');
});

require __DIR__ . '/auth.php';
