<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;
use App\Models\Building;
use App\Models\Land;
use App\Models\WaterService;
use App\Models\ElectricityService;
use App\Models\ReInnovation;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $actionFilter = $request->get('action', 'all');
        $typeFilter = $request->get('type', 'all');
        $search = $request->get('search', '');

        // Collect all activities
        $activities = collect();

        // Sites activities
        if ($typeFilter === 'all' || $typeFilter === 'site') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(Site::latest('created_at')->get()->map(fn($item) => [
                    'type' => 'site',
                    'action' => 'created',
                    'icon' => 'geo-alt-fill',
                    'color' => 'success',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => $item->governorate_name_en,
                    'route' => route('sites.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(Site::latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'site',
                    'action' => 'updated',
                    'icon' => 'geo-alt-fill',
                    'color' => 'primary',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => $item->governorate_name_en,
                    'route' => route('sites.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(Site::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'site',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => $item->governorate_name_en,
                    'route' => route('sites.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Buildings activities
        if ($typeFilter === 'all' || $typeFilter === 'building') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(Building::with('site')->latest('created_at')->get()->map(fn($item) => [
                    'type' => 'building',
                    'action' => 'created',
                    'icon' => 'building-fill',
                    'color' => 'success',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => $item->site->name ?? 'N/A',
                    'route' => route('buildings.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(Building::with('site')->latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'building',
                    'action' => 'updated',
                    'icon' => 'building-fill',
                    'color' => 'primary',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => $item->site->name ?? 'N/A',
                    'route' => route('buildings.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(Building::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'building',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => $item->name,
                    'subtitle' => $item->code,
                    'description' => 'Deleted',
                    'route' => route('buildings.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Lands activities
        if ($typeFilter === 'all' || $typeFilter === 'land') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(Land::with('site')->latest('created_at')->get()->map(fn($item) => [
                    'type' => 'land',
                    'action' => 'created',
                    'icon' => 'map-fill',
                    'color' => 'success',
                    'title' => 'Plot ' . $item->plot_number,
                    'subtitle' => 'Basin ' . $item->basin,
                    'description' => $item->site->name ?? 'N/A',
                    'route' => route('lands.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(Land::with('site')->latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'land',
                    'action' => 'updated',
                    'icon' => 'map-fill',
                    'color' => 'primary',
                    'title' => 'Plot ' . $item->plot_number,
                    'subtitle' => 'Basin ' . $item->basin,
                    'description' => $item->site->name ?? 'N/A',
                    'route' => route('lands.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(Land::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'land',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => 'Plot ' . $item->plot_number,
                    'subtitle' => 'Basin ' . $item->basin,
                    'description' => 'Deleted',
                    'route' => route('lands.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Water Services
        if ($typeFilter === 'all' || $typeFilter === 'water') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(WaterService::with('building')->latest('created_at')->get()->map(fn($item) => [
                    'type' => 'water',
                    'action' => 'created',
                    'icon' => 'droplet-fill',
                    'color' => 'success',
                    'title' => 'Water Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => $item->building->name ?? 'N/A',
                    'route' => route('water-services.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(WaterService::with('building')->latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'water',
                    'action' => 'updated',
                    'icon' => 'droplet-fill',
                    'color' => 'primary',
                    'title' => 'Water Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => $item->building->name ?? 'N/A',
                    'route' => route('water-services.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(WaterService::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'water',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => 'Water Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => 'Deleted',
                    'route' => route('water-services.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Electricity Services
        if ($typeFilter === 'all' || $typeFilter === 'electricity') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(ElectricityService::with('building')->latest('created_at')->get()->map(fn($item) => [
                    'type' => 'electricity',
                    'action' => 'created',
                    'icon' => 'lightning-charge-fill',
                    'color' => 'success',
                    'title' => 'Electricity Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => $item->building->name ?? 'N/A',
                    'route' => route('electricity-services.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(ElectricityService::with('building')->latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'electricity',
                    'action' => 'updated',
                    'icon' => 'lightning-charge-fill',
                    'color' => 'primary',
                    'title' => 'Electricity Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => $item->building->name ?? 'N/A',
                    'route' => route('electricity-services.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(ElectricityService::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'electricity',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => 'Electricity Service',
                    'subtitle' => 'Reg# ' . $item->registration_number,
                    'description' => 'Deleted',
                    'route' => route('electricity-services.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Re-Innovations
        if ($typeFilter === 'all' || $typeFilter === 'innovation') {
            if ($actionFilter === 'all' || $actionFilter === 'created') {
                $activities = $activities->merge(ReInnovation::with('innovatable')->latest('created_at')->get()->map(fn($item) => [
                    'type' => 'innovation',
                    'action' => 'created',
                    'icon' => 'lightbulb-fill',
                    'color' => 'success',
                    'title' => $item->name,
                    'subtitle' => number_format($item->cost, 2) . ' JOD',
                    'description' => class_basename($item->innovatable_type) . ': ' . ($item->innovatable->name ?? 'N/A'),
                    'route' => route('re-innovations.show', $item),
                    'timestamp' => $item->created_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'updated') {
                $activities = $activities->merge(ReInnovation::with('innovatable')->latest('updated_at')->where('updated_at', '>', \DB::raw('created_at'))->get()->map(fn($item) => [
                    'type' => 'innovation',
                    'action' => 'updated',
                    'icon' => 'lightbulb-fill',
                    'color' => 'primary',
                    'title' => $item->name,
                    'subtitle' => number_format($item->cost, 2) . ' JOD',
                    'description' => class_basename($item->innovatable_type) . ': ' . ($item->innovatable->name ?? 'N/A'),
                    'route' => route('re-innovations.show', $item),
                    'timestamp' => $item->updated_at,
                ]));
            }
            if ($actionFilter === 'all' || $actionFilter === 'deleted') {
                $activities = $activities->merge(ReInnovation::onlyTrashed()->latest('deleted_at')->get()->map(fn($item) => [
                    'type' => 'innovation',
                    'action' => 'deleted',
                    'icon' => 'trash-fill',
                    'color' => 'danger',
                    'title' => $item->name,
                    'subtitle' => number_format($item->cost, 2) . ' JOD',
                    'description' => 'Deleted',
                    'route' => route('re-innovations.deleted'),
                    'timestamp' => $item->deleted_at,
                ]));
            }
        }

        // Apply search filter
        if ($search) {
            $activities = $activities->filter(function ($activity) use ($search) {
                return stripos($activity['title'], $search) !== false ||
                    stripos($activity['subtitle'], $search) !== false ||
                    stripos($activity['description'], $search) !== false;
            });
        }

        // Sort and paginate
        $activities = $activities->sortByDesc('timestamp')->values();

        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $activities->count();
        $activities = $activities->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $activities,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('activities.index', [
            'activities' => $paginator,
            'actionFilter' => $actionFilter,
            'typeFilter' => $typeFilter,
            'search' => $search,
        ]);
    }
}
