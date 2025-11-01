<?php

namespace App\Traits;

use App\Models\Activity;

trait LogsActivity
{
  /**
   * Boot the trait
   */
  protected static function bootLogsActivity(): void
  {
    static::created(function ($model) {
      if (static::shouldLogActivity('created')) {
        log_activity('created', $model);
      }
    });

    static::updated(function ($model) {
      if (static::shouldLogActivity('updated')) {
        $changes = $model->getChanges();
        unset($changes['updated_at']); // Don't log timestamp updates

        if (!empty($changes)) {
          log_activity('updated', $model, [
            'old' => $model->getOriginal(),
            'new' => $changes,
          ]);
        }
      }
    });

    static::deleted(function ($model) {
      if (static::shouldLogActivity('deleted')) {
        $action = $model->isForceDeleting() ? 'force_deleted' : 'deleted';
        log_activity($action, $model);
      }
    });

    if (method_exists(static::class, 'restored')) {
      static::restored(function ($model) {
        if (static::shouldLogActivity('restored')) {
          log_activity('restored', $model);
        }
      });
    }
  }

  /**
   * Determine if activity should be logged for this action
   */
  protected static function shouldLogActivity(string $action): bool
  {
    // You can override this in models to customize when to log
    // For example: protected static $logActivityActions = ['created', 'deleted'];

    if (property_exists(static::class, 'logActivityActions')) {
      return in_array($action, static::$logActivityActions);
    }

    // By default, log all actions
    return true;
  }

  /**
   * Get the name to use in activity descriptions
   */
  public function getActivityName(): string
  {
    return $this->name ?? $this->code ?? $this->registration_number ?? "#{$this->id}";
  }

  /**
   * Get contextual description for activity (override in models for custom context)
   */
  public function getActivityContext(): ?string
  {
    // Default context based on model type
    $modelClass = class_basename($this);

    switch ($modelClass) {
      case 'Image':
        return $this->getImageContext();
      case 'WaterReading':
        return $this->getWaterReadingContext();
      case 'ElectricReading':
        return $this->getElectricReadingContext();
      case 'WaterService':
        return $this->getWaterServiceContext();
      case 'ElectricityService':
        return $this->getElectricityServiceContext();
      case 'ElectricServiceDisconnection':
        return $this->getDisconnectionContext();
      case 'Building':
        return $this->getBuildingContext();
      case 'Land':
        return $this->getLandContext();
      case 'ZoningStatus':
        return $this->getZoningContext();
      case 'Renovation':
        return $this->getRenovationContext();
      default:
        return null;
    }
  }

  /**
   * Context helpers for specific models
   */
  protected function getImageContext(): ?string
  {
    if (!$this->relationLoaded('imageable')) {
      $this->load('imageable');
    }
    if (!$this->imageable) return null;
    $type = class_basename($this->imageable_type);
    $name = $this->imageable->name ?? $this->imageable->code ?? '#' . $this->imageable->id;
    return "for {$type}: {$name}";
  }

  protected function getWaterReadingContext(): ?string
  {
    if (!$this->relationLoaded('waterService')) {
      $this->load('waterService.building');
    }
    if (!$this->waterService) return null;
    $building = $this->waterService->building->name ?? '#' . $this->waterService->building_id;
    return "for Water Service (Reg# {$this->waterService->registration_number}) - Building: {$building}";
  }

  protected function getElectricReadingContext(): ?string
  {
    if (!$this->relationLoaded('electricityService')) {
      $this->load('electricityService.building');
    }
    if (!$this->electricityService) return null;
    $building = $this->electricityService->building->name ?? '#' . $this->electricityService->building_id;
    return "for Electricity Service (Reg# {$this->electricityService->registration_number}) - Building: {$building}";
  }

  protected function getWaterServiceContext(): ?string
  {
    if (!$this->relationLoaded('building')) {
      $this->load('building');
    }
    if (!$this->building) return null;
    return "for Building: {$this->building->name} ({$this->building->code})";
  }

  protected function getElectricityServiceContext(): ?string
  {
    if (!$this->relationLoaded('building')) {
      $this->load('building');
    }
    if (!$this->building) return null;
    return "for Building: {$this->building->name} ({$this->building->code})";
  }

  protected function getDisconnectionContext(): ?string
  {
    if (!$this->relationLoaded('electricityService')) {
      $this->load('electricityService.building');
    }
    if (!$this->electricityService) return null;
    $building = $this->electricityService->building->name ?? '#' . $this->electricityService->building_id;
    return "for Electricity Service (Reg# {$this->electricityService->registration_number}) - Building: {$building}";
  }

  protected function getBuildingContext(): ?string
  {
    if (!$this->relationLoaded('site')) {
      $this->load('site');
    }
    if (!$this->site) return null;
    return "at Site: {$this->site->name} ({$this->site->code})";
  }

  protected function getLandContext(): ?string
  {
    if (!$this->relationLoaded('site')) {
      $this->load('site');
    }
    if (!$this->site) return null;
    return "at Site: {$this->site->name} ({$this->site->code})";
  }

  protected function getZoningContext(): ?string
  {
    // ZoningStatus uses many-to-many relationships, not polymorphic
    // So we can't get a single context, return null
    return null;
  }

  protected function getRenovationContext(): ?string
  {
    if (!$this->relationLoaded('innovatable')) {
      $this->load('innovatable');
    }
    if (!$this->innovatable) return null;
    $type = class_basename($this->innovatable_type);
    $name = $this->innovatable->name ?? $this->innovatable->code ?? '#' . $this->innovatable->id;
    return "for {$type}: {$name}";
  }

  /**
   * Get all activities for this model
   */
  public function activities()
  {
    return Activity::where('subject_type', static::class)
      ->where('subject_id', $this->id)
      ->latest()
      ->get();
  }
}
