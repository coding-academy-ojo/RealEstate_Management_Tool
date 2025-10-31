<?php

namespace App\Http\Controllers;

use App\Models\ElectricServiceDisconnection;
use App\Models\ElectricityService;
use Illuminate\Http\Request;

class ElectricServiceDisconnectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:electricity');
    }

    public function store(Request $request, ElectricityService $electricityService)
    {
        $data = $this->validateDisconnection($request);
        $data['electric_service_id'] = $electricityService->id;

        ElectricServiceDisconnection::create($data);

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Disconnection record saved successfully.');
    }

    public function update(
        Request $request,
        ElectricityService $electricityService,
        ElectricServiceDisconnection $disconnection
    ) {
        $this->ensureBelongsToService($electricityService, $disconnection);

        $data = $this->validateDisconnection($request);
        $disconnection->update($data);

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Disconnection record updated successfully.');
    }

    public function destroy(ElectricityService $electricityService, ElectricServiceDisconnection $disconnection)
    {
        $this->ensureBelongsToService($electricityService, $disconnection);

        $disconnection->delete();

        return redirect()
            ->route('electricity-services.show', $electricityService)
            ->with('success', 'Disconnection record removed successfully.');
    }

    private function validateDisconnection(Request $request): array
    {
        $request->merge([
            'disconnection_date' => $this->nullIfEmpty($request->input('disconnection_date')),
            'reconnection_date' => $this->nullIfEmpty($request->input('reconnection_date')),
        ]);

        return $request->validate([
            'disconnection_date' => 'nullable|date',
            'reconnection_date' => 'nullable|date|after_or_equal:disconnection_date',
            'reason' => 'nullable|string',
        ]);
    }

    private function ensureBelongsToService(
        ElectricityService $electricityService,
        ElectricServiceDisconnection $disconnection
    ): void {
        if ($disconnection->electric_service_id !== $electricityService->id) {
            abort(404);
        }
    }

    private function nullIfEmpty(?string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
