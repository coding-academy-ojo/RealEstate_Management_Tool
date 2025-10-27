<?php

namespace App\Http\Controllers;

use App\Models\ReInnovation;
use Illuminate\Http\Request;

class ReInnovationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [
            'search' => $request->input('search'),
            'type' => $request->input('type'),
        ];

        // Get sort parameters
        $sort = $request->input('sort', 'number');
        $direction = $request->input('direction', 'asc');

        // Build query
        $query = ReInnovation::with('innovatable');

        // Apply search filter
        if ($filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Apply type filter
        if ($filters['type']) {
            $query->where('innovatable_type', $filters['type']);
        }

        // Apply sorting
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'type':
                $query->orderBy('innovatable_type', $direction);
                break;
            case 'cost':
                $query->orderBy('cost', $direction);
                break;
            case 'date':
                $query->orderBy('date', $direction);
                break;
            case 'number':
            default:
                // For number column, reverse the direction for data sorting
                $actualDirection = $direction === 'asc' ? 'desc' : 'asc';
                $query->orderBy('id', $actualDirection);
                break;
        }

        // Get distinct types for filter dropdown
        $types = [
            'App\Models\Site' => 'Site',
            'App\Models\Building' => 'Building',
            'App\Models\Land' => 'Land',
        ];

        $reInnovations = $query->paginate(15)->withQueryString();

        return view('re-innovations.index', compact('reInnovations', 'types', 'filters', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('re-innovations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'innovatable_type' => 'required|string|in:App\Models\Site,App\Models\Building,App\Models\Land',
            'innovatable_id' => 'required|integer',
            'cost' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        ReInnovation::create($validated);

        return redirect()->route('re-innovations.index')
            ->with('success', 'Re-innovation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReInnovation $reInnovation)
    {
        $reInnovation->load('innovatable');

        return view('re-innovations.show', compact('reInnovation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReInnovation $reInnovation)
    {
        $reInnovation->delete();

        return redirect()->route('re-innovations.index')
            ->with('success', 'Re-innovation deleted successfully.');
    }

    /**
     * Display deleted re-innovations
     */
    public function deleted()
    {
        $reInnovations = ReInnovation::onlyTrashed()
            ->with('innovatable')
            ->latest('deleted_at')
            ->paginate(15);
        return view('re-innovations.deleted', compact('reInnovations'));
    }

    /**
     * Restore a soft deleted re-innovation
     */
    public function restore($id)
    {
        $reInnovation = ReInnovation::onlyTrashed()->findOrFail($id);
        $reInnovation->restore();
        return redirect()->route('re-innovations.deleted')->with('success', 'Re-innovation restored successfully!');
    }

    /**
     * Permanently delete a re-innovation
     */
    public function forceDelete($id)
    {
        $reInnovation = ReInnovation::onlyTrashed()->findOrFail($id);
        $reInnovation->forceDelete();
        return redirect()->route('re-innovations.deleted')->with('success', 'Re-innovation permanently deleted!');
    }
}
