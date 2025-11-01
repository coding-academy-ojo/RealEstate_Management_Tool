<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Image;
use App\Models\Land;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function __construct()
    {
        // View-only access for showing images, all other operations require specific privileges
        $this->middleware('privilege:sites_lands_buildings')->only(['upload', 'update', 'setPrimary', 'destroy', 'reorder']);
    }

    /**
     * Upload images for a model.
     */
    public function upload(Request $request, $type, $id)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'titles.*' => 'nullable|string|max:255',
            'descriptions.*' => 'nullable|string',
        ]);

        $model = $this->getModel($type, $id);

        if (!$model) {
            return back()->with('error', 'Entity not found.');
        }

        $uploadedCount = 0;

        $uploadedFilesInfo = [];

        if ($request->hasFile('images')) {
            $uploadedFiles = $request->file('images');
            $hasExistingImages = $model->images()->exists();
            $currentMaxOrder = $model->images()->max('order') ?? -1;
            $folderName = Str::plural(Str::snake(class_basename($model)));

            Log::debug('Uploading gallery images', [
                'request_type' => $type,
                'resolved_folder' => $folderName,
                'model_class' => get_class($model),
                'model_id' => $model->getKey(),
                'existing_images' => $hasExistingImages,
                'starting_order' => $currentMaxOrder,
            ]);

            foreach ($uploadedFiles as $index => $file) {
                $currentMaxOrder++;
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs("images/{$folderName}/{$model->getKey()}", $filename, 'private');

                $isPrimary = !$hasExistingImages && $uploadedCount === 0;

                Log::debug('Storing gallery image file', [
                    'model' => get_class($model),
                    'model_id' => $model->getKey(),
                    'original_name' => $file->getClientOriginalName(),
                    'stored_filename' => $filename,
                    'disk_path' => $path,
                    'is_primary' => $isPrimary,
                    'order' => $currentMaxOrder,
                ]);

                $model->images()->create([
                    'filename' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'title' => $request->titles[$index] ?? null,
                    'description' => $request->descriptions[$index] ?? null,
                    'order' => $currentMaxOrder,
                    'is_primary' => $isPrimary,
                ]);

                $uploadedFilesInfo[] = [
                    'original' => $file->getClientOriginalName(),
                    'stored_as' => $filename,
                ];

                $uploadedCount++;
            }
        }

        if ($uploadedCount > 0) {
            $list = collect($uploadedFilesInfo)
                ->pluck('original')
                ->map(fn($name) => "\"{$name}\"")
                ->implode(', ');

            return back()->with('success', "Uploaded {$uploadedCount} image(s): {$list}.");
        }

        return back()->with('warning', 'No images were uploaded. Please try again.');
    }

    /**
     * Update image details.
     */
    public function update(Request $request, Image $image)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $image->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Image updated successfully.');
    }

    /**
     * Set an image as primary.
     */
    public function setPrimary(Image $image)
    {
        // Remove primary status from all images of this entity
        Image::where('imageable_type', $image->imageable_type)
            ->where('imageable_id', $image->imageable_id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated successfully.');
    }

    /**
     * Delete an image.
     */
    public function destroy(Request $request, Image $image)
    {
        Log::debug('Deleting gallery image', [
            'image_id' => $image->id,
            'imageable_type' => $image->imageable_type,
            'imageable_id' => $image->imageable_id,
        ]);

        $wasPrimary = $image->is_primary;
        $imageableType = $image->imageable_type;
        $imageableId = $image->imageable_id;

        $image->delete();

        // If deleted image was primary, set the first remaining image as primary
        if ($wasPrimary) {
            $firstImage = Image::where('imageable_type', $imageableType)
                ->where('imageable_id', $imageableId)
                ->orderBy('order')
                ->first();

            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request, $type, $id)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|exists:images,id',
        ]);

        foreach ($request->images as $order => $imageId) {
            Image::where('id', $imageId)->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'Images reordered successfully.']);
    }

    /**
     * Get the model instance.
     */
    private function getModel($type, $id)
    {
        return match ($type) {
            'site' => Site::findOrFail($id),
            'building' => Building::findOrFail($id),
            'land' => Land::findOrFail($id),
            default => null,
        };
    }

    /**
     * Display image.
     */
    public function show(Image $image)
    {
        $path = $image->path;

        // Try to find the file in private disk first, then public
        $disk = null;
        if (Storage::disk('private')->exists($path)) {
            $disk = 'private';
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public';
        }

        if (!$disk) {
            abort(404, 'Image not found');
        }

        $absolutePath = Storage::disk($disk)->path($path);
        $mimeType = $image->mime_type ?: mime_content_type($absolutePath);

        return response()->file($absolutePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
