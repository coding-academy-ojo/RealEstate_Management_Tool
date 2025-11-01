<?php

use App\Http\Controllers\ElectricityBillController;
use App\Http\Controllers\ElectricityCompanyController;
use App\Http\Controllers\ElectricityOverviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterCompanyController;
use App\Http\Controllers\WaterOverviewController;
use App\Http\Controllers\WaterServiceController;
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
        'total_innovations' => \App\Models\Renovation::count(),
        'total_water_services' => \App\Models\WaterService::count(),
        'total_electricity_services' => \App\Models\ElectricityService::count(),

        // Water service stats
        'total_water_readings' => \App\Models\WaterReading::count(),
        'unpaid_water_bills' => \App\Models\WaterReading::where('is_paid', false)->whereNotNull('bill_amount')->count(),
        'total_water_outstanding' => \App\Models\WaterReading::where('is_paid', false)->sum('bill_amount'),

        // Electricity service stats
        'total_electricity_readings' => \App\Models\ElectricReading::count(),
        'unpaid_electricity_bills' => \App\Models\ElectricReading::where('is_paid', false)->whereNotNull('bill_amount')->count(),
        'total_electricity_outstanding' => \App\Models\ElectricReading::where('is_paid', false)->sum('bill_amount'),

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

        // Recent Activity - From Activity Log System
        'recent_activities' => \App\Models\Activity::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return app(\App\Http\Controllers\ActivityController::class)->transformActivityForView($activity);
            }),

        // Innovation costs
        'total_innovation_cost' => \App\Models\Renovation::sum('cost'),

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
        return response()->json(\App\Models\Land::with('site:id,code,name')
            ->select(
                'id',
                'site_id',
                'plot_key',
                'directorate',
                'directorate_number',
                'village',
                'village_number',
                'basin',
                'basin_number',
                'neighborhood',
                'plot_number'
            )
            ->get()
            ->map(function ($land) {
                return [
                    'id' => $land->id,
                    'site_id' => $land->site_id,
                    'site_name' => $land->site->name ?? null,
                    'plot_key' => $land->plot_key,
                    'directorate' => $land->directorate,
                    'directorate_number' => $land->directorate_number,
                    'village' => $land->village,
                    'village_number' => $land->village_number,
                    'basin' => $land->basin,
                    'basin_number' => $land->basin_number,
                    'neighborhood' => $land->neighborhood,
                    'plot_number' => $land->plot_number,
                ];
            }));
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

    Route::get('sites/{site}/documents/{document}', [\App\Http\Controllers\SiteController::class, 'document'])
        ->name('sites.documents.show');

    Route::get('lands/{land}/documents/{document}', [\App\Http\Controllers\LandController::class, 'document'])
        ->name('lands.documents.show');

    Route::get('water-services/deleted/list', [\App\Http\Controllers\WaterServiceController::class, 'deleted'])
        ->middleware('privilege:water')
        ->name('water-services.deleted');
    Route::post('water-services/{id}/restore', [\App\Http\Controllers\WaterServiceController::class, 'restore'])
        ->middleware('privilege:water')
        ->name('water-services.restore');
    Route::delete('water-services/{id}/force-delete', [\App\Http\Controllers\WaterServiceController::class, 'forceDelete'])
        ->middleware('privilege:water')
        ->name('water-services.force-delete');
    Route::get('water-services/{waterService}/files/{document}', [\App\Http\Controllers\WaterServiceController::class, 'file'])
        ->name('water-services.files.show');
    Route::get('water-services/{waterService}/readings/{waterReading}/files/{document}', [\App\Http\Controllers\WaterReadingController::class, 'file'])
        ->name('water-services.readings.files.show');
    Route::get('water/overview', [WaterOverviewController::class, 'overview'])
        ->name('water.overview');

    Route::get('water/bills', [\App\Http\Controllers\WaterReadingController::class, 'index'])
        ->name('water.bills.index');

    Route::get('water/index', [WaterServiceController::class, 'index'])
        ->name('water.services.index');

    Route::prefix('water/companies')
        ->name('water.companies.')
        ->group(function () {
            Route::get('/', [WaterCompanyController::class, 'index'])->name('index');
        Route::post('/', [WaterCompanyController::class, 'store'])->middleware('privilege:water')->name('store');
        Route::put('{waterCompany}', [WaterCompanyController::class, 'update'])->middleware('privilege:water')->name('update');
        Route::delete('{waterCompany}', [WaterCompanyController::class, 'destroy'])->middleware('privilege:water')->name('destroy');
        Route::post('{company}/restore', [WaterCompanyController::class, 'restore'])->middleware('privilege:water')->name('restore');
        });

    Route::post('water-services/{waterService}/readings', [\App\Http\Controllers\WaterReadingController::class, 'store'])
        ->middleware('privilege:water')
        ->name('water-services.readings.store');
    Route::put('water-services/{waterService}/readings/{waterReading}', [\App\Http\Controllers\WaterReadingController::class, 'update'])
        ->middleware('privilege:water')
        ->name('water-services.readings.update');
    Route::delete('water-services/{waterService}/readings/{waterReading}', [\App\Http\Controllers\WaterReadingController::class, 'destroy'])
        ->middleware('privilege:water')
        ->name('water-services.readings.destroy');
    Route::post('water-services/{waterService}/deactivate', [\App\Http\Controllers\WaterServiceController::class, 'deactivate'])
        ->middleware('privilege:water')
        ->name('water-services.deactivate');
    Route::post('water-services/{waterService}/reactivate', [\App\Http\Controllers\WaterServiceController::class, 'reactivate'])
        ->middleware('privilege:water')
        ->name('water-services.reactivate');

    // Water services - viewing allowed for all, editing requires privilege
    Route::resource('water-services', \App\Http\Controllers\WaterServiceController::class)
        ->except(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('water-services', \App\Http\Controllers\WaterServiceController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('privilege:water');

    Route::get('electricity-services/{electricityService}/files/{document}', [\App\Http\Controllers\ElectricityServiceController::class, 'file'])
        ->name('electricity-services.files.show');
    Route::get('electricity-services/{electricityService}/readings/{electricReading}/files/{document}', [\App\Http\Controllers\ElectricReadingController::class, 'file'])
        ->name('electricity-services.readings.files.show');
    Route::post('electricity-services/{electricityService}/readings', [\App\Http\Controllers\ElectricReadingController::class, 'store'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.readings.store');
    Route::put('electricity-services/{electricityService}/readings/{electricReading}', [\App\Http\Controllers\ElectricReadingController::class, 'update'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.readings.update');
    Route::delete('electricity-services/{electricityService}/readings/{electricReading}', [\App\Http\Controllers\ElectricReadingController::class, 'destroy'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.readings.destroy');

    Route::get('electricity/overview', [ElectricityOverviewController::class, 'index'])
        ->name('electricity.overview');

    Route::get('electricity/bills', [ElectricityBillController::class, 'index'])
        ->name('electricity.bills.index');

    Route::get('electricity/index', [\App\Http\Controllers\ElectricityServiceController::class, 'index'])
        ->name('electricity.services.index');

    Route::prefix('electricity/companies')
        ->name('electricity.companies.')
        ->group(function () {
            Route::get('/', [ElectricityCompanyController::class, 'index'])->name('index');
        Route::post('/', [ElectricityCompanyController::class, 'store'])->middleware('privilege:electricity')->name('store');
        Route::put('{electricityCompany}', [ElectricityCompanyController::class, 'update'])->middleware('privilege:electricity')->name('update');
        Route::delete('{electricityCompany}', [ElectricityCompanyController::class, 'destroy'])->middleware('privilege:electricity')->name('destroy');
        Route::post('{company}/restore', [ElectricityCompanyController::class, 'restore'])->middleware('privilege:electricity')->name('restore');
        });

    Route::post('electricity-services/{electricityService}/disconnections', [\App\Http\Controllers\ElectricServiceDisconnectionController::class, 'store'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.disconnections.store');
    Route::put('electricity-services/{electricityService}/disconnections/{disconnection}', [\App\Http\Controllers\ElectricServiceDisconnectionController::class, 'update'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.disconnections.update');
    Route::delete('electricity-services/{electricityService}/disconnections/{disconnection}', [\App\Http\Controllers\ElectricServiceDisconnectionController::class, 'destroy'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.disconnections.destroy');

    Route::get('electricity-services/deleted/list', [\App\Http\Controllers\ElectricityServiceController::class, 'deleted'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.deleted');
    Route::post('electricity-services/{id}/restore', [\App\Http\Controllers\ElectricityServiceController::class, 'restore'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.restore');
    Route::delete('electricity-services/{id}/force-delete', [\App\Http\Controllers\ElectricityServiceController::class, 'forceDelete'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.force-delete');
    Route::post('electricity-services/{electricityService}/deactivate', [\App\Http\Controllers\ElectricityServiceController::class, 'deactivate'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.deactivate');
    Route::post('electricity-services/{electricityService}/reactivate', [\App\Http\Controllers\ElectricityServiceController::class, 'reactivate'])
        ->middleware('privilege:electricity')
        ->name('electricity-services.reactivate');

    // Electricity services - viewing allowed for all, editing requires privilege
    Route::resource('electricity-services', \App\Http\Controllers\ElectricityServiceController::class)
        ->only(['index', 'show']);
    Route::resource('electricity-services', \App\Http\Controllers\ElectricityServiceController::class)
        ->except(['index', 'show'])
        ->middleware('privilege:electricity');

    Route::get('renovations/deleted/list', [\App\Http\Controllers\RenovationController::class, 'deleted'])
        ->middleware('privilege:renovation')
        ->name('renovations.deleted');
    Route::post('renovations/{id}/restore', [\App\Http\Controllers\RenovationController::class, 'restore'])
        ->middleware('privilege:renovation')
        ->name('renovations.restore');
    Route::delete('renovations/{id}/force-delete', [\App\Http\Controllers\RenovationController::class, 'forceDelete'])
        ->middleware('privilege:renovation')
        ->name('renovations.force-delete');
    Route::resource('renovations', \App\Http\Controllers\RenovationController::class);

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
