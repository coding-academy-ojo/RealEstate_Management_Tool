<?php

namespace App\Http\Controllers;

use App\Models\WaterReading;
use App\Models\WaterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WaterReadingController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:water');
  }

  public function store(Request $request, WaterService $waterService)
  {
    $data = $this->validateReading($request);
    $data['water_service_id'] = $waterService->id;
    $data['is_paid'] = $request->boolean('is_paid');

    if ($request->hasFile('meter_image')) {
      $data['meter_image'] = $request->file('meter_image')->store('water-services/readings/meters', 'private');
    }

    if ($request->hasFile('bill_image')) {
      $data['bill_image'] = $request->file('bill_image')->store('water-services/readings/bills', 'private');
    }

    WaterReading::create($data);
    $this->recalculateReadings($waterService);

    return redirect()->route('water-services.show', $waterService)
      ->with('success', 'Water reading added successfully.');
  }

  public function update(Request $request, WaterService $waterService, WaterReading $waterReading)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $data = $this->validateReading($request, $waterReading->id);
    $data['is_paid'] = $request->boolean('is_paid');

    if ($request->hasFile('meter_image')) {
      $this->deleteStoredFile($waterReading->meter_image);
      $data['meter_image'] = $request->file('meter_image')->store('water-services/readings/meters', 'private');
    }

    if ($request->hasFile('bill_image')) {
      $this->deleteStoredFile($waterReading->bill_image);
      $data['bill_image'] = $request->file('bill_image')->store('water-services/readings/bills', 'private');
    }

    $waterReading->update($data);
    $this->recalculateReadings($waterService);

    return redirect()->route('water-services.show', $waterService)
      ->with('success', 'Water reading updated successfully.');
  }

  public function destroy(WaterService $waterService, WaterReading $waterReading)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $this->deleteStoredFile($waterReading->meter_image);
    $this->deleteStoredFile($waterReading->bill_image);

    $waterReading->delete();
    $this->recalculateReadings($waterService);

    return redirect()->route('water-services.show', $waterService)
      ->with('success', 'Water reading deleted successfully.');
  }

  public function file(WaterService $waterService, WaterReading $waterReading, string $document)
  {
    $this->ensureBelongsToService($waterService, $waterReading);

    $attribute = match ($document) {
      'meter' => 'meter_image',
      'bill' => 'bill_image',
      default => abort(404),
    };

    $path = $waterReading->{$attribute};
    $disk = $this->resolveDiskForPath($path);

    if (!$path || !$disk) {
      abort(404, 'File not found.');
    }

    $absolutePath = Storage::disk($disk)->path($path);

    if (request()->boolean('download')) {
      return response()->download($absolutePath, basename($path));
    }

    $mime = mime_content_type($absolutePath) ?: 'application/octet-stream';

    return response()->file($absolutePath, [
      'Content-Type' => $mime,
    ]);
  }

  private function validateReading(Request $request, ?int $readingId = null): array
  {
    return $request->validate([
      'current_reading' => 'required|numeric|min:0',
      'bill_amount' => 'nullable|numeric|min:0',
      'is_paid' => 'nullable|boolean',
      'reading_date' => 'required|date',
      'meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
      'bill_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
      'notes' => 'nullable|string',
    ]);
  }

  private function recalculateReadings(WaterService $waterService): void
  {
    $readings = $waterService->readings()->orderBy('reading_date')->orderBy('id')->get();
    $previous = 0.0;
    $isFirst = true;

    foreach ($readings as $reading) {
      $current = (float) ($reading->current_reading ?? 0);
      $consumption = $isFirst
        ? $current
        : max(0, round($current - $previous, 2));

      $reading->forceFill([
        'consumption_value' => $consumption,
      ])->saveQuietly();

      $previous = $current;
      $isFirst = false;
    }
  }

  private function ensureBelongsToService(WaterService $waterService, WaterReading $waterReading): void
  {
    if ($waterReading->water_service_id !== $waterService->id) {
      abort(404);
    }
  }

  private function deleteStoredFile(?string $path): void
  {
    $disk = $this->resolveDiskForPath($path);

    if ($disk) {
      try {
        Storage::disk($disk)->delete($path);
      } catch (\Throwable $exception) {
        // Ignore disk errors during cleanup
      }
    }
  }

  private function resolveDiskForPath(?string $path): ?string
  {
    if (!$path) {
      return null;
    }

    foreach (['private', 'public'] as $disk) {
      try {
        if (Storage::disk($disk)->exists($path)) {
          return $disk;
        }
      } catch (\Throwable $exception) {
        // Skip disks that are not configured
      }
    }

    return null;
  }
}
