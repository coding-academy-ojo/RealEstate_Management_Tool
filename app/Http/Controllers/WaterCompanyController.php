<?php

namespace App\Http\Controllers;

use App\Models\WaterCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaterCompanyController extends Controller
{
    /**
     * Display all water companies with management tools.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'active');
        $search = $request->input('search');

        $query = WaterCompany::query()->withCount('services');

        if ($status === 'inactive') {
            $query->onlyTrashed();
        } elseif ($status === 'all') {
            $query->withTrashed();
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $companies = $query
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => WaterCompany::withTrashed()->count(),
            'active' => WaterCompany::count(),
            'inactive' => WaterCompany::onlyTrashed()->count(),
        ];

        return view('water.companies.index', compact('companies', 'stats', 'status', 'search'));
    }

    /**
     * Store a new water company for quick selection.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:water_companies,name',
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $company = WaterCompany::create($validated);

        if ($request->expectsJson()) {
            return response()->json($company, 201);
        }

        return redirect()
            ->route('water.companies.index')
            ->with('success', 'Water company added successfully.');
    }

    /**
     * Update a water company.
     */
    public function update(Request $request, WaterCompany $waterCompany): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('water_companies', 'name')->ignore($waterCompany->id),
            ],
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $waterCompany->update($validated);

        return redirect()
            ->route('water.companies.index')
            ->with('success', 'Water company updated successfully.');
    }

    /**
     * Soft delete (deactivate) a water company.
     */
    public function destroy(WaterCompany $waterCompany): RedirectResponse
    {
        $waterCompany->delete();

        return redirect()
            ->route('water.companies.index')
            ->with('success', 'Water company deactivated successfully.');
    }

    /**
     * Restore a soft deleted company.
     */
    public function restore(int $companyId): RedirectResponse
    {
        $company = WaterCompany::onlyTrashed()->findOrFail($companyId);
        $company->restore();

        return redirect()
            ->route('water.companies.index', ['status' => 'inactive'])
            ->with('success', 'Water company restored successfully.');
    }
}
