# Activity Logging System Documentation

## Overview

This activity logging system tracks all user actions across your Real Estate Management application.

## What Gets Logged

-   **Who**: The user who performed the action (with fallback to "System" if no user)
-   **What**: The action performed (created, updated, deleted, restored, etc.)
-   **When**: Timestamp of when the action occurred
-   **Where**: IP address and user agent
-   **Subject**: What was acted upon (Site, Building, Land, Water Service, etc.)
-   **Details**: Additional properties like old/new values

---

## Usage Methods

### Method 1: Using the Helper Function (Simplest)

```php
// Basic usage - logs automatically
log_activity('created', $site);

// With additional data
log_activity('updated', $building, [
    'old' => $oldData,
    'new' => $newData
]);

// With custom description
log_activity('deleted', $land, [], 'Admin deleted land parcel #123');
```

### Method 2: Using the Fluent Logger (Recommended)

```php
// Create
activity()->created()->on($site)->log();

// Update with properties
activity()
    ->updated()
    ->on($building)
    ->withProperties(['field' => 'value'])
    ->log();

// Delete with custom description
activity()
    ->deleted()
    ->on($waterService)
    ->withDescription('Service disconnected for non-payment')
    ->log();

// Custom action
activity()
    ->performedAction('uploaded_document')
    ->on($land)
    ->withProperties(['filename' => 'deed.pdf'])
    ->log();
```

### Method 3: Automatic Logging with Trait (Best for Models)

Add the trait to your model:

```php
use App\Traits\LogsActivity;

class Site extends Model
{
    use LogsActivity;

    // All create, update, delete, restore actions will be logged automatically!
}
```

To customize what gets logged:

```php
class Site extends Model
{
    use LogsActivity;

    // Only log creates and deletes
    protected static $logActivityActions = ['created', 'deleted'];

    // Customize the name used in activity descriptions
    public function getActivityName(): string
    {
        return "{$this->name} ({$this->code})";
    }
}
```

---

## Real-World Examples

### Example 1: Site Controller

```php
public function store(Request $request)
{
    $site = Site::create($validated);

    // Manual logging
    log_activity('created', $site);

    // OR using fluent API
    activity()->created()->on($site)->log();

    // OR add LogsActivity trait to Site model - automatic!

    return redirect()->route('sites.show', $site);
}

public function update(Request $request, Site $site)
{
    $oldData = $site->toArray();
    $site->update($validated);

    activity()
        ->updated()
        ->on($site)
        ->withProperties([
            'old' => $oldData,
            'new' => $site->toArray()
        ])
        ->log();

    return redirect()->route('sites.show', $site);
}
```

### Example 2: Image Upload

```php
public function upload(Request $request, $type, $id)
{
    $model = $this->getModel($type, $id);

    foreach ($request->file('images') as $file) {
        $image = $model->images()->create([...]);

        activity()
            ->performedAction('uploaded_image')
            ->on($model)
            ->withProperties([
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ])
            ->log();
    }
}
```

### Example 3: Water Service Deactivation

```php
public function deactivate(WaterService $waterService)
{
    $waterService->update(['is_active' => false]);

    activity()
        ->performedAction('deactivated')
        ->on($waterService)
        ->withDescription(auth()->user()->name . ' deactivated water service ' . $waterService->registration_number)
        ->log();
}
```

---

## Viewing Activities

### Get All Activities for a Model

```php
// In your controller
$site = Site::find($id);
$activities = $site->activities(); // Returns activities for this specific site

// Or query directly
$activities = Activity::where('subject_type', Site::class)
    ->where('subject_id', $site->id)
    ->with('user')
    ->latest()
    ->get();
```

### Get Recent Activities (for Dashboard)

```php
$recentActivities = Activity::with('user')
    ->latest()
    ->take(10)
    ->get();

foreach ($recentActivities as $activity) {
    echo $activity->description; // "John Doe created Al-Mahata Site"
    echo $activity->created_at->diffForHumans(); // "2 minutes ago"
    echo $activity->user->name; // "John Doe"
}
```

### Filter Activities

```php
// By action
$creates = Activity::where('action', 'created')->get();

// By user
$userActivities = Activity::where('user_id', auth()->id())->get();

// By subject type
$siteActivities = Activity::where('subject_type', Site::class)->get();

// Date range
$todayActivities = Activity::whereDate('created_at', today())->get();
```

---

## Adding the Trait to Your Models

Add `LogsActivity` trait to automatically log all actions:

```php
// app/Models/Site.php
use App\Traits\LogsActivity;

class Site extends Model
{
    use SoftDeletes, LogsActivity;
}

// app/Models/Building.php
class Building extends Model
{
    use SoftDeletes, LogsActivity;
}

// app/Models/Land.php
class Land extends Model
{
    use SoftDeletes, LogsActivity;
}

// app/Models/WaterService.php
class WaterService extends Model
{
    use SoftDeletes, LogsActivity;
}

// app/Models/ElectricityService.php
class ElectricityService extends Model
{
    use SoftDeletes, LogsActivity;
}

// app/Models/Renovation.php
class Renovation extends Model
{
    use SoftDeletes, LogsActivity;
}
```

Once you add the trait, all create/update/delete/restore actions will be automatically logged!

---

## Activity Properties

Each activity record contains:

-   `id` - Unique identifier
-   `user_id` - Who performed the action (nullable)
-   `action` - What was done (created, updated, deleted, restored, etc.)
-   `subject_type` - Model class name (App\Models\Site)
-   `subject_id` - ID of the model
-   `subject_name` - Cached name for quick display
-   `description` - Human-readable description
-   `properties` - JSON field for additional data
-   `ip_address` - User's IP
-   `user_agent` - Browser/device info
-   `created_at` - When it happened

---

## Best Practices

1. **Use the trait for models** - Add `LogsActivity` trait to your models for automatic logging
2. **Use fluent API for manual logging** - `activity()->created()->on($model)->log()`
3. **Include meaningful properties** - Store old/new values for updates
4. **Custom actions are OK** - Use descriptive action names like 'uploaded_document', 'sent_email', etc.
5. **Don't log sensitive data** - Avoid logging passwords, tokens, etc. in properties

---

## Quick Reference

```php
// Simple
log_activity('created', $model);

// Fluent
activity()->created()->on($model)->log();
activity()->updated()->on($model)->withProperties($data)->log();
activity()->deleted()->on($model)->withDescription($text)->log();

// Automatic
class YourModel extends Model {
    use LogsActivity;
}
```
