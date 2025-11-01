<?php

namespace App\Http\Controllers;

use App\Models\ZoningStatus;
use Illuminate\Http\Request;

class ZoningStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:sites_lands_buildings');
    }

    /**
     * Store a newly created zoning status
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255|unique:zoning_statuses,name_ar',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = true;

        $zoningStatus = ZoningStatus::create($validated);

        return response()->json($zoningStatus, 201);
    }
}
