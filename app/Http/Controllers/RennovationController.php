<?php

namespace App\Http\Controllers;

use App\Models\Rennovation;
use Illuminate\Http\Request;

class RennovationController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:rennovation')->except(['index', 'show']);
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $filters = [
      'search' => $request->input('search'),
      'type' => $request->input('type'),
    ];

    $sort = $request->input('sort', 'number');
    $direction = $request->input('direction', 'asc');

    $query = Rennovation::with('innovatable');

    if ($filters['search']) {
      $query->where(function ($q) use ($filters) {
        $q->where('name', 'like', "%{$filters['search']}%")
          ->orWhere('description', 'like', "%{$filters['search']}%");
      });
    }

    if ($filters['type']) {
      $query->where('innovatable_type', $filters['type']);
    }

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
        $actualDirection = $direction === 'asc' ? 'desc' : 'asc';
        $query->orderBy('id', $actualDirection);
        break;
    }

    $types = [
      'App\\Models\\Site' => 'Site',
      'App\\Models\\Building' => 'Building',
      'App\\Models\\Land' => 'Land',
    ];

    $rennovations = $query->paginate(15)->withQueryString();

    return view('rennovations.index', compact('rennovations', 'types', 'filters', 'sort', 'direction'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('rennovations.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'innovatable_type' => 'required|string|in:App\\Models\\Site,App\\Models\\Building,App\\Models\\Land',
      'innovatable_id' => 'required|integer',
      'cost' => 'required|numeric|min:0',
      'date' => 'required|date',
      'description' => 'nullable|string',
    ]);

    Rennovation::create($validated);

    return redirect()->route('rennovations.index')
      ->with('success', 'Rennovation created successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Rennovation $rennovation)
  {
    $rennovation->load('innovatable');

    return view('rennovations.show', compact('rennovation'));
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
  public function destroy(Rennovation $rennovation)
  {
    $rennovation->delete();

    return redirect()->route('rennovations.index')
      ->with('success', 'Rennovation deleted successfully.');
  }

  public function deleted()
  {
    $rennovations = Rennovation::onlyTrashed()
      ->with('innovatable')
      ->latest('deleted_at')
      ->paginate(15);

    return view('rennovations.deleted', compact('rennovations'));
  }

  public function restore($id)
  {
    $rennovation = Rennovation::onlyTrashed()->findOrFail($id);
    $rennovation->restore();

    return redirect()->route('rennovations.deleted')->with('success', 'Rennovation restored successfully!');
  }

  public function forceDelete($id)
  {
    $rennovation = Rennovation::onlyTrashed()->findOrFail($id);
    $rennovation->forceDelete();

    return redirect()->route('rennovations.deleted')->with('success', 'Rennovation permanently deleted!');
  }
}
