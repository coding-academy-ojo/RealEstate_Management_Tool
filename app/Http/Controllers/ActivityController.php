<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Site;
use App\Models\Building;
use App\Models\Land;
use App\Models\WaterService;
use App\Models\ElectricityService;
use App\Models\Renovation;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $actionFilter = $request->get('action', 'all');
        $typeFilter = $request->get('type', 'all');
        $search = $request->get('search', '');

        // Query activities from database
        $query = Activity::with('user')->latest();

        // Filter by action
        if ($actionFilter !== 'all') {
            $query->where('action', $actionFilter);
        }

        // Filter by entity type
        if ($typeFilter !== 'all') {
            $typeMap = [
                'site' => Site::class,
                'building' => Building::class,
                'land' => Land::class,
                'water' => WaterService::class,
                'electricity' => ElectricityService::class,
                'innovation' => Renovation::class,
                'water_reading' => 'App\\Models\\WaterReading',
                'electric_reading' => 'App\\Models\\ElectricReading',
                'water_company' => 'App\\Models\\WaterCompany',
                'electricity_company' => 'App\\Models\\ElectricityCompany',
                'disconnection' => 'App\\Models\\ElectricServiceDisconnection',
                'image' => 'App\\Models\\Image',
                'zoning' => 'App\\Models\\ZoningStatus',
            ];

            if (isset($typeMap[$typeFilter])) {
                $query->where('subject_type', $typeMap[$typeFilter]);
            }
        }

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(20)->withQueryString();

        // Transform activities for view
        $activities->getCollection()->transform(function ($activity) {
            return $this->transformActivityForView($activity);
        });

        return view('activities.index', [
            'activities' => $activities,
            'actionFilter' => $actionFilter,
            'typeFilter' => $typeFilter,
            'search' => $search,
        ]);
    }

    /**
     * Transform activity record for display
     */
    public function transformActivityForView($activity)
    {
        $typeMap = [
            Site::class => 'site',
            Building::class => 'building',
            Land::class => 'land',
            WaterService::class => 'water',
            ElectricityService::class => 'electricity',
            Renovation::class => 'innovation',
            'App\\Models\\WaterReading' => 'water_reading',
            'App\\Models\\ElectricReading' => 'electric_reading',
            'App\\Models\\Image' => 'image',
            'App\\Models\\WaterCompany' => 'water_company',
            'App\\Models\\ElectricityCompany' => 'electricity_company',
            'App\\Models\\ElectricServiceDisconnection' => 'disconnection',
            'App\\Models\\ZoningStatus' => 'zoning',
        ];

        $iconMap = [
            Site::class => 'geo-alt-fill',
            Building::class => 'building-fill',
            Land::class => 'map-fill',
            WaterService::class => 'droplet-fill',
            ElectricityService::class => 'lightning-charge-fill',
            Renovation::class => 'lightbulb-fill',
            'App\\Models\\WaterReading' => 'droplet-half',
            'App\\Models\\ElectricReading' => 'lightning-fill',
            'App\\Models\\Image' => 'image-fill',
            'App\\Models\\WaterCompany' => 'building',
            'App\\Models\\ElectricityCompany' => 'plug-fill',
            'App\\Models\\ElectricServiceDisconnection' => 'power',
            'App\\Models\\ZoningStatus' => 'rulers',
        ];

        $colorMap = [
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            'restored' => 'info',
            'force_deleted' => 'danger',
        ];

        $type = $typeMap[$activity->subject_type] ?? 'unknown';
        $icon = $iconMap[$activity->subject_type] ?? 'circle-fill';
        $color = $colorMap[$activity->action] ?? 'secondary';

        // Get route based on type
        $route = $this->getActivityRoute($activity);

        // Enhanced title and subtitle based on activity type
        $title = $activity->formatted_description; // Use the formatted description from Activity model
        $subtitle = $activity->user ? $activity->user->name : 'System';
        $userRole = null;

        if ($activity->user) {
            // Get user role - check is_super_admin first, then specific role field
            if ($activity->user->is_super_admin) {
                $userRole = 'Super Admin';
            } elseif (isset($activity->user->role)) {
                // Map role to display name
                $userRole = match ($activity->user->role) {
                    'super_admin' => 'Super Admin',
                    'admin' => 'Admin',
                    'engineer' => 'Engineer',
                    default => ucfirst($activity->user->role),
                };
            } else {
                // Fallback to privileges if role field doesn't exist
                $privileges = [];
                if ($activity->user->privilege_water) $privileges[] = 'Water';
                if ($activity->user->privilege_electricity) $privileges[] = 'Electricity';
                if ($activity->user->privilege_sites_lands_buildings) $privileges[] = 'Sites/Lands/Buildings';
                if ($activity->user->privilege_renovation) $privileges[] = 'Renovation';
                $userRole = !empty($privileges) ? implode(', ', $privileges) : 'User';
            }
        }

        // Extract additional details from properties for description
        $additionalInfo = '';
        if ($activity->properties && is_array($activity->properties)) {
            if (isset($activity->properties['context'])) {
                $additionalInfo = $activity->properties['context'];
            }
        }

        // Use original description as fallback if formatted is same as description
        $description = $additionalInfo ?: $activity->subject_type_name;

        return [
            'type' => $type,
            'action' => $activity->action,
            'icon' => $icon,
            'color' => $color,
            'title' => $title, // This is the formatted description
            'subtitle' => $subtitle,
            'user_role' => $userRole,
            'description' => $description, // Additional context
            'route' => $route,
            'timestamp' => $activity->created_at,
            'ip_address' => $activity->ip_address,
            'user_agent' => $activity->user_agent,
        ];
    }

    /**
     * Get the appropriate route for an activity
     */
    protected function getActivityRoute($activity)
    {
        try {
            $subject = $activity->subject();

            if (!$subject) {
                return '#';
            }

            return match ($activity->subject_type) {
                Site::class => route('sites.show', $subject),
                Building::class => route('buildings.show', $subject),
                Land::class => route('lands.show', $subject),
                WaterService::class => route('water-services.show', $subject),
                ElectricityService::class => route('electricity-services.show', $subject),
                Renovation::class => route('renovations.show', $subject),
                'App\\Models\\WaterReading' => $subject->waterService ? route('water-services.show', $subject->waterService) : '#',
                'App\\Models\\ElectricReading' => $subject->electricityService ? route('electricity-services.show', $subject->electricityService) : '#',
                'App\\Models\\Image' => $this->getImageableRoute($subject),
                'App\\Models\\WaterCompany' => route('water.companies.index'),
                'App\\Models\\ElectricityCompany' => route('electricity.companies.index'),
                'App\\Models\\ElectricServiceDisconnection' => $subject->electricityService ? route('electricity-services.show', $subject->electricityService) : '#',
                'App\\Models\\ZoningStatus' => $this->getZonableRoute($subject),
                default => '#',
            };
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * Get route for imageable entities
     */
    protected function getImageableRoute($image)
    {
        if (!$image->imageable) return '#';

        return match ($image->imageable_type) {
            Site::class => route('sites.show', $image->imageable),
            Building::class => route('buildings.show', $image->imageable),
            Land::class => route('lands.show', $image->imageable),
            default => '#',
        };
    }

    /**
     * Get route for zonable entities
     */
    protected function getZonableRoute($zoning)
    {
        if (!$zoning->zonable) return '#';

        return match ($zoning->zonable_type) {
            Site::class => route('sites.show', $zoning->zonable),
            Building::class => route('buildings.show', $zoning->zonable),
            Land::class => route('lands.show', $zoning->zonable),
            default => '#',
        };
    }
}
