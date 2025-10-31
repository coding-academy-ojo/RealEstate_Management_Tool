<?php

namespace App\Http\Controllers;

use App\Models\ElectricityCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectricityCompanyController extends Controller
{
    /**
     * Store a new electricity company for quick selection.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:electricity_companies,name',
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $company = ElectricityCompany::create($validated);

        return response()->json($company, 201);
    }
}
