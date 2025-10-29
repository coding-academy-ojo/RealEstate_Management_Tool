<?php

namespace App\Http\Controllers;

use App\Models\Renovation;
use Illuminate\Http\Request;

class RenovationController extends Controller
{
  public function __construct()
  {
    $this->middleware('privilege:renovation')->except(['index', 'show']);
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

    $query = Renovation::with('innovatable');

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

    $renovations = $query->paginate(15)->withQueryString();

    return view('renovations.index', compact('renovations', 'types', 'filters', 'sort', 'direction'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('renovations.create');
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

    Renovation::create($validated);

    return redirect()->route('renovations.index')
      ->with('success', 'Renovation created successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Renovation $renovation)
  {
    $renovation->load('innovatable');

    return view('renovations.show', compact('renovation'));
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
  public function destroy(Renovation $renovation)
  {
    $renovation->delete();

    return redirect()->route('renovations.index')
      ->with('success', 'Renovation deleted successfully.');
  }

  public function deleted()
  {
    $renovations = Renovation::onlyTrashed()
      ->with('innovatable')
      ->latest('deleted_at')
      ->paginate(15);

    return view('renovations.deleted', compact('renovations'));
  }

  public function restore($id)
  {
    $renovation = Renovation::onlyTrashed()->findOrFail($id);
    $renovation->restore();

    return redirect()->route('renovations.deleted')->with('success', 'Renovation restored successfully!');
  }

  public function forceDelete($id)
  {
    $renovation = Renovation::onlyTrashed()->findOrFail($id);
    $renovation->forceDelete();

    return redirect()->route('renovations.deleted')->with('success', 'Renovation permanently deleted!');
  }
}
