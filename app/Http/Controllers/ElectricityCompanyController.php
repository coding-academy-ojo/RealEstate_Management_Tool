<?php

namespace App\Http\Controllers;

use App\Models\ElectricityCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ElectricityCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('privilege:electricity');
    }

    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->input('search')),
            'status' => $request->input('status', 'active'),
        ];

        $stats = [
            'total' => ElectricityCompany::withTrashed()->count(),
            'active' => ElectricityCompany::count(),
            'inactive' => ElectricityCompany::onlyTrashed()->count(),
        ];

        $companiesQuery = ElectricityCompany::query()
            ->withCount('services')
            ->orderBy('name');

        if ($filters['status'] === 'inactive') {
            $companiesQuery->onlyTrashed();
        } elseif ($filters['status'] === 'all') {
            $companiesQuery->withTrashed();
        }

        if ($filters['search']) {
            $companiesQuery->where(function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('name_ar', 'like', "%{$filters['search']}%")
                    ->orWhere('website', 'like', "%{$filters['search']}%");
            });
        }

        $companies = $companiesQuery->paginate(12)->withQueryString();

        return view('electricity.companies.index', [
            'companies' => $companies,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('electricity_companies', 'name'),
            ],
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $company = ElectricityCompany::create($validated);

        if ($request->expectsJson()) {
            return response()->json($company, 201);
        }

        return redirect()
            ->route('electricity.companies.index')
            ->with('success', 'Electricity company added successfully.');
    }

    public function update(Request $request, ElectricityCompany $electricityCompany): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('electricity_companies', 'name')->ignore($electricityCompany->id),
            ],
            'name_ar' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $electricityCompany->update($validated);

        return redirect()
            ->route('electricity.companies.index', $request->query())
            ->with('success', 'Electricity company updated successfully.');
    }

    public function destroy(ElectricityCompany $electricityCompany): RedirectResponse
    {
        $electricityCompany->delete();

        return redirect()
            ->route('electricity.companies.index')
            ->with('success', 'Electricity company deactivated successfully.');
    }

    public function restore(int $company): RedirectResponse
    {
        $record = ElectricityCompany::onlyTrashed()->findOrFail($company);
        $record->restore();

        return redirect()
            ->route('electricity.companies.index', ['status' => 'inactive'])
            ->with('success', 'Electricity company restored successfully.');
    }
}
