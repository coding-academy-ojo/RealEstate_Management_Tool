# 🎯 Activity Logging System - Quick Start Guide

## ✅ What's Been Set Up

Your Real Estate Management app now has a complete activity logging system that tracks:

-   ✅ **Who** did the action (user name)
-   ✅ **What** they did (created, updated, deleted, restored)
-   ✅ **When** it happened (timestamp)
-   ✅ **What** was affected (Site, Building, Land, Water/Electricity Service, Renovation)
-   ✅ **Additional details** (IP address, user agent, old/new values)

---

## 🚀 Quick Start (3 Simple Steps)

### Step 1: Add the Trait to Your Models

Open each model and add `use LogsActivity;`:

```php
// app/Models/Site.php ✅ ALREADY DONE!
use App\Traits\LogsActivity;

class Site extends Model
{
    use SoftDeletes, LogsActivity;  // ✅ Already added!
}
```

**Do the same for these models:**

```php
// app/Models/Building.php
use App\Traits\LogsActivity;

class Building extends Model
{
    use SoftDeletes, LogsActivity;  // Add this line
}

// app/Models/Land.php
use App\Traits\LogsActivity;

class Land extends Model
{
    use SoftDeletes, LogsActivity;  // Add this line
}

// app/Models/WaterService.php
use App\Traits\LogsActivity;

class WaterService extends Model
{
    use SoftDeletes, LogsActivity;  // Add this line
}

// app/Models/ElectricityService.php
use App\Traits\LogsActivity;

class ElectricityService extends Model
{
    use SoftDeletes, LogsActivity;  // Add this line
}

// app/Models/Renovation.php
use App\Traits\LogsActivity;

class Renovation extends Model
{
    use SoftDeletes, LogsActivity;  // Add this line
}
```

### Step 2: Test It!

Just create, update, or delete anything in your app - it's automatically logged!

```php
// Create a new site - automatically logged!
$site = Site::create([...]);

// Update it - automatically logged!
$site->update(['name' => 'New Name']);

// Delete it - automatically logged!
$site->delete();

// Restore it - automatically logged!
$site->restore();
```

### Step 3: View Activities

Go to: `http://realstate.test/activities`

You'll see all activities with:

-   Filters for action type (Created/Updated/Deleted)
-   Filters for entity type (Sites/Buildings/Lands/Services)
-   Search functionality
-   Who did it and when

---

## 📝 Manual Logging (When Needed)

Sometimes you want to log custom actions (like uploading files, sending emails, etc.)

### Simple Way:

```php
// Log image upload
log_activity('uploaded_image', $site);

// Log with extra data
log_activity('updated', $building, [
    'field_changed' => 'address',
    'old_value' => '123 Old St',
    'new_value' => '456 New St'
]);
```

### Fluent Way (Recommended):

```php
// Basic
activity()->created()->on($site)->log();

// With properties
activity()
    ->updated()
    ->on($building)
    ->withProperties(['changes' => $data])
    ->log();

// Custom action
activity()
    ->performedAction('sent_email')
    ->on($waterService)
    ->withDescription('Sent payment reminder email')
    ->log();
```

---

## 🎨 Real Examples from Your App

### Example 1: When Creating a Site

**Currently in your `SiteController@store`:**

```php
public function store(Request $request)
{
    $site = Site::create($validated);

    // ✅ Activity is AUTOMATICALLY logged because Site uses LogsActivity trait!
    // Description: "John Doe created Al-Mahata Site"

    return redirect()->route('sites.show', $site);
}
```

### Example 2: When Uploading Images

**In your `ImageController@upload`:**

```php
public function upload(Request $request, $type, $id)
{
    $model = $this->getModel($type, $id);

    foreach ($request->file('images') as $image) {
        $model->images()->create([...]);

        // Log the image upload
        activity()
            ->performedAction('uploaded_image')
            ->on($model)
            ->withProperties([
                'filename' => $image->getClientOriginalName()
            ])
            ->log();
    }
}
```

### Example 3: When Deactivating Water Service

**In your `WaterServiceController`:**

```php
public function deactivate(WaterService $waterService)
{
    $waterService->update(['is_active' => false]);

    // Custom activity log
    activity()
        ->performedAction('deactivated')
        ->on($waterService)
        ->withDescription(auth()->user()->name . ' deactivated service due to non-payment')
        ->log();
}
```

---

## 📊 Viewing Activities

### Option 1: Activities Page (Already Set Up!)

Visit: `/activities` or click "View All Activities" on dashboard

### Option 2: In Your Code

```php
// Get all activities
$activities = Activity::with('user')->latest()->get();

// Get activities for a specific site
$site = Site::find(1);
$siteActivities = $site->activities();

// Get recent activities (for dashboard)
$recent = Activity::with('user')->latest()->take(10)->get();

foreach ($recent as $activity) {
    echo $activity->description;  // "John Doe created Al-Mahata Site"
    echo $activity->user->name;   // "John Doe"
    echo $activity->created_at->diffForHumans();  // "5 minutes ago"
}

// Filter by action
$creates = Activity::where('action', 'created')->get();
$updates = Activity::where('action', 'updated')->get();
$deletes = Activity::where('action', 'deleted')->get();

// Filter by entity type
$siteActivities = Activity::where('subject_type', Site::class)->get();
```

---

## 🎯 Summary

### What's Automatic (No Code Needed):

-   ✅ Creating records
-   ✅ Updating records
-   ✅ Deleting records
-   ✅ Restoring records

### What Needs Manual Logging:

-   📤 Uploading files
-   📧 Sending emails
-   🔒 Deactivating services
-   🎨 Custom actions

### Quick Reference:

```php
// Automatic (just add trait)
use LogsActivity;

// Manual simple
log_activity('action', $model);

// Manual fluent
activity()->created()->on($model)->log();
```

---

## 🔥 Next Steps

1. **Add the trait** to Building, Land, WaterService, ElectricityService, Renovation models
2. **Test it** by creating a new site - check `/activities`
3. **Add manual logging** for image uploads and other custom actions
4. **Enjoy!** Your app now tracks everything!

---

## 📚 Full Documentation

See `ACTIVITY_SYSTEM.md` for detailed documentation with all options and examples.
