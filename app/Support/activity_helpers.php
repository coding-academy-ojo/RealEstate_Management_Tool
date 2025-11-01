<?php

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

if (! function_exists('log_activity')) {
  /**
   * Log an activity action
   *
   * @param string $action The action performed (created, updated, deleted, etc.)
   * @param Model $subject The model that was acted upon
   * @param array $properties Additional data to store (old/new values)
   * @param string|null $description Optional description override
   * @return Activity
   */
  function log_activity(string $action, Model $subject, array $properties = [], ?string $description = null): Activity
  {
    $user = Auth::user();

    // Get subject name
    $subjectName = method_exists($subject, 'getActivityName')
      ? $subject->getActivityName()
      : ($subject->name ?? $subject->code ?? "#{$subject->id}");

    // Get contextual information
    $context = method_exists($subject, 'getActivityContext')
      ? $subject->getActivityContext()
      : null;

    // Add context to properties so it's accessible by the Activity model's formatted_description
    if ($context) {
      $properties['context'] = $context;
    }

    // Build automatic description if not provided
    if (!$description) {
      $userName = $user ? $user->name : 'System';

      $description = match ($action) {
        'created' => "{$userName} created {$subjectName}" . ($context ? " {$context}" : ""),
        'updated' => "{$userName} updated {$subjectName}" . ($context ? " {$context}" : ""),
        'deleted' => "{$userName} deleted {$subjectName}" . ($context ? " {$context}" : ""),
        'restored' => "{$userName} restored {$subjectName}" . ($context ? " {$context}" : ""),
        'uploaded_image' => "{$userName} uploaded image to {$subjectName}" . ($context ? " {$context}" : ""),
        'deleted_image' => "{$userName} deleted image from {$subjectName}" . ($context ? " {$context}" : ""),
        default => "{$userName} performed {$action} on {$subjectName}" . ($context ? " {$context}" : ""),
      };
    }

    return Activity::create([
      'user_id' => $user?->id,
      'action' => $action,
      'subject_type' => get_class($subject),
      'subject_id' => $subject->getKey(),
      'subject_name' => $subjectName,
      'description' => $description,
      'properties' => $properties ?: null,
      'ip_address' => request()->ip(),
      'user_agent' => request()->userAgent(),
    ]);
  }
}

if (! function_exists('activity')) {
  /**
   * Fluent activity logger
   *
   * @return \App\Support\ActivityLogger
   */
  function activity(): \App\Support\ActivityLogger
  {
    return new \App\Support\ActivityLogger();
  }
}
