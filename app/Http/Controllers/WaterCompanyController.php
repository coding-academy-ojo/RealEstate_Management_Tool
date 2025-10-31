<?php

namespace App\Http\Controllers;

use App\Models\WaterCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaterCompanyController extends Controller
{
    /**
     * Store a new water company for quick selection.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:water_companies,name',
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $company = WaterCompany::create($validated);

        return response()->json($company, 201);
    }
}
