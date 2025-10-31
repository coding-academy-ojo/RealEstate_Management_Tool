<?php

namespace App\Http\Controllers;

use App\Models\ElectricReading;
use App\Models\ElectricityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElectricReadingController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:electricity');
    }

    public function store(Request $request, ElectricityService $electricityService)
    {
        $data = $this->validateReading($request, $electricityService);
        $data['electric_service_id'] = $electricityService->id;
        $data['is_paid'] = $request->boolean('is_paid');

        if ($request->hasFile('meter_image')) {
            $data['meter_image'] = $request->file('meter_image')->store('electricity-services/readings/meters', 'private');
        }

        if ($request->hasFile('bill_image')) {
            $data['bill_image'] = $request->file('bill_image')->store('electricity-services/readings/bills', 'private');
        }

        ElectricReading::create($data);

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Electricity reading added successfully.');
    }

    public function update(Request $request, ElectricityService $electricityService, ElectricReading $electricReading)
    {
        $this->ensureBelongsToService($electricityService, $electricReading);

        $data = $this->validateReading($request, $electricityService);
        $data['is_paid'] = $request->boolean('is_paid');

        if ($request->hasFile('meter_image')) {
            $this->deleteStoredFile($electricReading->meter_image);
            $data['meter_image'] = $request->file('meter_image')->store('electricity-services/readings/meters', 'private');
        }

        if ($request->hasFile('bill_image')) {
            $this->deleteStoredFile($electricReading->bill_image);
            $data['bill_image'] = $request->file('bill_image')->store('electricity-services/readings/bills', 'private');
        }

        $electricReading->update($data);

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Electricity reading updated successfully.');
    }

    public function destroy(ElectricityService $electricityService, ElectricReading $electricReading)
    {
        $this->ensureBelongsToService($electricityService, $electricReading);

        $this->deleteStoredFile($electricReading->meter_image);
        $this->deleteStoredFile($electricReading->bill_image);

        $electricReading->delete();

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Electricity reading deleted successfully.');
    }

    public function file(ElectricityService $electricityService, ElectricReading $electricReading, string $document)
    {
        $this->ensureBelongsToService($electricityService, $electricReading);

        $attribute = match ($document) {
            'meter' => 'meter_image',
            'bill' => 'bill_image',
            default => abort(404),
        };

        $path = $electricReading->{$attribute};
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

    private function validateReading(Request $request, ElectricityService $service): array
    {
        $request->merge([
            'imported_current' => $this->nullIfEmpty($request->input('imported_current')),
            'imported_calculated' => $this->nullIfEmpty($request->input('imported_calculated')),
            'produced_current' => $this->nullIfEmpty($request->input('produced_current')),
            'produced_calculated' => $this->nullIfEmpty($request->input('produced_calculated')),
            'saved_energy' => $this->nullIfEmpty($request->input('saved_energy')),
            'bill_amount' => $this->nullIfEmpty($request->input('bill_amount')),
            'reading_date' => $this->nullIfEmpty($request->input('reading_date')),
        ]);

        if ($service->has_solar_power) {
            $rules = [
                'imported_current' => ['required', 'numeric', 'min:0'],
                'imported_calculated' => ['required', 'numeric', 'min:0'],
                'produced_current' => ['required', 'numeric', 'min:0'],
                'produced_calculated' => ['required', 'numeric', 'min:0'],
                'saved_energy' => ['required', 'numeric', 'min:0'],
                'bill_amount' => 'nullable|numeric|min:0',
                'is_paid' => 'nullable|boolean',
                'reading_date' => 'nullable|date',
                'meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'bill_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'notes' => 'nullable|string',
            ];
        } else {
            $rules = [
                'imported_current' => ['required', 'numeric', 'min:0'],
                'imported_calculated' => ['required', 'numeric', 'min:0'],
                'produced_current' => ['nullable', 'numeric', 'min:0'],
                'produced_calculated' => ['nullable', 'numeric', 'min:0'],
                'saved_energy' => ['nullable', 'numeric', 'min:0'],
                'bill_amount' => 'nullable|numeric|min:0',
                'is_paid' => 'nullable|boolean',
                'reading_date' => 'nullable|date',
                'meter_image' => 'nullable|file|mimes:jpg,jpeg,png|max:4096',
                'bill_image' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'notes' => 'nullable|string',
            ];
        }

        $validated = $request->validate($rules);

        if (!$service->has_solar_power) {
            $validated['produced_current'] = null;
            $validated['produced_calculated'] = null;
            $validated['saved_energy'] = null;
        }

        return $validated;
    }

    private function ensureBelongsToService(ElectricityService $electricityService, ElectricReading $electricReading): void
    {
        if ($electricReading->electric_service_id !== $electricityService->id) {
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

    private function nullIfEmpty(?string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
