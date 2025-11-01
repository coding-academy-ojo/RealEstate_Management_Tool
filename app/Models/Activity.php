<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'subject_name',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic relation without morphTo for better control)
     */
    public function subject()
    {
        $class = $this->subject_type;

        if (class_exists($class)) {
            return $class::find($this->subject_id);
        }

        return null;
    }

    /**
     * Format action for display
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'uploaded_image' => 'Uploaded Image',
            'deleted_image' => 'Deleted Image',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get a fully formatted human-readable description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $user = $this->user ? $this->user->name : 'System';
        $entityType = $this->subject_type_name;
        $entityName = $this->subject_name ?? 'Unknown ' . $entityType;

        // Get parent/context information from properties if available
        $context = '';
        if (isset($this->properties['context'])) {
            $context = ' at ' . $this->properties['context'];
        }

        // Build human-readable description based on entity type and action
        return match ($this->subject_type) {
            'App\\Models\\Image' => $this->formatImageActivity($user, $entityName, $context),
            'App\\Models\\WaterReading' => $this->formatReadingActivity($user, 'Water', $entityName, $context),
            'App\\Models\\ElectricReading' => $this->formatReadingActivity($user, 'Electricity', $entityName, $context),
            'App\\Models\\WaterService' => $this->formatServiceActivity($user, 'Water', $entityName, $context),
            'App\\Models\\ElectricityService' => $this->formatServiceActivity($user, 'Electricity', $entityName, $context),
            'App\\Models\\ElectricServiceDisconnection' => $this->formatDisconnectionActivity($user, $entityName, $context),
            'App\\Models\\Site' => $this->formatSiteActivity($user, $entityName),
            'App\\Models\\Building' => $this->formatBuildingActivity($user, $entityName, $context),
            'App\\Models\\Land' => $this->formatLandActivity($user, $entityName, $context),
            'App\\Models\\Renovation' => $this->formatRenovationActivity($user, $entityName, $context),
            'App\\Models\\WaterCompany' => $this->formatCompanyActivity($user, 'Water', $entityName),
            'App\\Models\\ElectricityCompany' => $this->formatCompanyActivity($user, 'Electricity', $entityName),
            'App\\Models\\ZoningStatus' => $this->formatZoningActivity($user, $entityName, $context),
            default => $this->formatDefaultActivity($user, $entityType, $entityName),
        };
    }

    /**
     * Format image-related activities
     */
    protected function formatImageActivity(string $user, string $name, string $context): string
    {
        // Extract the location from context (e.g., "Site: Karak Castle Visitors Complex")
        $location = '';
        if ($context && preg_match('/for (Site|Building|Land): (.+)$/', $context, $matches)) {
            $locationType = $matches[1];
            $locationName = $matches[2];
            $location = " for {$locationType} {$locationName}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $location = $cleanContext ? " for {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} added image{$location}",
            'updated' => "{$user} updated image{$location}",
            'deleted' => "{$user} removed image{$location}",
            'restored' => "{$user} restored image{$location}",
            default => "{$user} {$this->action} image{$location}",
        };
    }

    /**
     * Format reading-related activities
     */
    protected function formatReadingActivity(string $user, string $type, string $name, string $context): string
    {
        // For readings, extract service and building info from context
        $location = '';
        if ($context && preg_match('/for (Building|Service): (.+)$/', $context, $matches)) {
            $location = " for {$matches[2]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $location = $cleanContext ? " for {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} added {$type} reading{$location}",
            'updated' => "{$user} updated {$type} reading{$location}",
            'deleted' => "{$user} removed {$type} reading{$location}",
            default => "{$user} {$this->action} {$type} reading{$location}",
        };
    }

    /**
     * Format service-related activities
     */
    protected function formatServiceActivity(string $user, string $type, string $name, string $context): string
    {
        // Extract building context if available
        $buildingContext = '';
        if ($context && preg_match('/for Building: (.+)$/', $context, $matches)) {
            $buildingContext = " for Building {$matches[1]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $buildingContext = $cleanContext ? " for {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} registered {$type} service{$buildingContext}",
            'updated' => "{$user} updated {$type} service{$buildingContext}",
            'deleted' => "{$user} removed {$type} service{$buildingContext}",
            'restored' => "{$user} restored {$type} service{$buildingContext}",
            default => "{$user} {$this->action} {$type} service{$buildingContext}",
        };
    }

    /**
     * Format disconnection activities
     */
    protected function formatDisconnectionActivity(string $user, string $name, string $context): string
    {
        // Extract service info from context
        $serviceContext = '';
        if ($context && preg_match('/for (Electricity )?Service: (.+)$/', $context, $matches)) {
            $serviceContext = " for Service {$matches[2]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $serviceContext = $cleanContext ? " for {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} recorded disconnection{$serviceContext}",
            'updated' => "{$user} updated disconnection{$serviceContext}",
            'deleted' => "{$user} removed disconnection{$serviceContext}",
            default => "{$user} {$this->action} disconnection{$serviceContext}",
        };
    }

    /**
     * Format site activities
     */
    protected function formatSiteActivity(string $user, string $name): string
    {
        return match ($this->action) {
            'created' => "{$user} created site {$name}",
            'updated' => "{$user} updated site {$name}",
            'deleted' => "{$user} removed site {$name}",
            'restored' => "{$user} restored site {$name}",
            default => "{$user} {$this->action} site {$name}",
        };
    }

    /**
     * Format building activities
     */
    protected function formatBuildingActivity(string $user, string $name, string $context): string
    {
        // Extract site context if available
        $siteContext = '';
        if ($context && preg_match('/at Site: (.+)$/', $context, $matches)) {
            $siteContext = " in Site {$matches[1]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $siteContext = $cleanContext ? " in {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} created building {$name}{$siteContext}",
            'updated' => "{$user} updated building {$name}{$siteContext}",
            'deleted' => "{$user} removed building {$name}{$siteContext}",
            'restored' => "{$user} restored building {$name}{$siteContext}",
            default => "{$user} {$this->action} building {$name}{$siteContext}",
        };
    }

    /**
     * Format land activities
     */
    protected function formatLandActivity(string $user, string $name, string $context): string
    {
        // For land, show plot info and site
        $landInfo = $name; // This will be like "Plot 23 in Basin 4"
        $siteContext = '';

        if ($context && preg_match('/at Site: (.+)$/', $context, $matches)) {
            // Extract site name from context
            $siteContext = " in Site {$matches[1]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $siteContext = $cleanContext ? " in {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} registered land {$landInfo}{$siteContext}",
            'updated' => "{$user} updated land {$landInfo}{$siteContext}",
            'deleted' => "{$user} removed land {$landInfo}{$siteContext}",
            'restored' => "{$user} restored land {$landInfo}{$siteContext}",
            default => "{$user} {$this->action} land {$landInfo}{$siteContext}",
        };
    }

    /**
     * Format renovation activities
     */
    protected function formatRenovationActivity(string $user, string $name, string $context): string
    {
        // Extract location context (Site/Building/Land)
        $locationContext = '';
        if ($context && preg_match('/for (Site|Building|Land): (.+)$/', $context, $matches)) {
            $locationContext = " for {$matches[1]} {$matches[2]}";
        } elseif ($context) {
            $cleanContext = trim(str_replace(['at ', 'for '], '', $context));
            $locationContext = $cleanContext ? " for {$cleanContext}" : '';
        }

        return match ($this->action) {
            'created' => "{$user} created renovation {$name}{$locationContext}",
            'updated' => "{$user} updated renovation {$name}{$locationContext}",
            'deleted' => "{$user} removed renovation {$name}{$locationContext}",
            'restored' => "{$user} restored renovation {$name}{$locationContext}",
            default => "{$user} {$this->action} renovation {$name}{$locationContext}",
        };
    }

    /**
     * Format company activities
     */
    protected function formatCompanyActivity(string $user, string $type, string $name): string
    {
        return match ($this->action) {
            'created' => "{$user} added {$type} company {$name}",
            'updated' => "{$user} updated {$type} company {$name}",
            'deleted' => "{$user} removed {$type} company {$name}",
            'restored' => "{$user} restored {$type} company {$name}",
            default => "{$user} {$this->action} {$type} company {$name}",
        };
    }

    /**
     * Format zoning status activities
     */
    protected function formatZoningActivity(string $user, string $name, string $context): string
    {
        // Extract location context
        $locationContext = '';
        if ($context && preg_match('/(Site|Building|Land): (.+)$/', $context, $matches)) {
            $locationContext = " for {$matches[1]} {$matches[2]}";
        } elseif ($context) {
            $locationContext = str_replace([' at ', ' for '], ' for ', $context);
        }

        return match ($this->action) {
            'created' => "{$user} added zoning information{$locationContext}",
            'updated' => "{$user} updated zoning status{$locationContext}",
            'deleted' => "{$user} removed zoning information{$locationContext}",
            default => "{$user} {$this->action} zoning status{$locationContext}",
        };
    }

    /**
     * Format default activities
     */
    protected function formatDefaultActivity(string $user, string $entityType, string $name): string
    {
        $action = match ($this->action) {
            'created' => 'created',
            'updated' => 'updated',
            'deleted' => 'deleted',
            'restored' => 'restored',
            default => $this->action,
        };

        return "{$user} {$action} {$entityType}: {$name}";
    }

    /**
     * Get the display name for the subject type
     */
    public function getSubjectTypeNameAttribute(): string
    {
        return match ($this->subject_type) {
            'App\\Models\\Site' => 'Site',
            'App\\Models\\Building' => 'Building',
            'App\\Models\\Land' => 'Land',
            'App\\Models\\WaterService' => 'Water Service',
            'App\\Models\\ElectricityService' => 'Electricity Service',
            'App\\Models\\Renovation' => 'Renovation',
            'App\\Models\\WaterReading' => 'Water Reading',
            'App\\Models\\ElectricReading' => 'Electric Reading',
            'App\\Models\\Image' => 'Image',
            'App\\Models\\WaterCompany' => 'Water Company',
            'App\\Models\\ElectricityCompany' => 'Electricity Company',
            'App\\Models\\ElectricServiceDisconnection' => 'Service Disconnection',
            'App\\Models\\ZoningStatus' => 'Zoning Status',
            default => class_basename($this->subject_type),
        };
    }
}
