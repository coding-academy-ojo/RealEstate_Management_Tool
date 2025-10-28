@extends('layouts.app')

@section('title', 'All Activities')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Activities</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-clock-history me-2 text-orange"></i>Activity Log
        </h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('activities.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ $search }}"
                        placeholder="Search activities...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Action Type</label>
                    <select class="form-select" name="action">
                        <option value="all" {{ $actionFilter === 'all' ? 'selected' : '' }}>All Actions</option>
                        <option value="created" {{ $actionFilter === 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ $actionFilter === 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ $actionFilter === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Entity Type</label>
                    <select class="form-select" name="type">
                        <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Entities</option>
                        <option value="site" {{ $typeFilter === 'site' ? 'selected' : '' }}>Sites</option>
                        <option value="building" {{ $typeFilter === 'building' ? 'selected' : '' }}>Buildings</option>
                        <option value="land" {{ $typeFilter === 'land' ? 'selected' : '' }}>Lands</option>
                        <option value="water" {{ $typeFilter === 'water' ? 'selected' : '' }}>Water Services</option>
                        <option value="electricity" {{ $typeFilter === 'electricity' ? 'selected' : '' }}>Electricity
                            Services</option>
                        <option value="innovation" {{ $typeFilter === 'innovation' ? 'selected' : '' }}>Rennovations
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-orange w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Activities Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2 text-orange"></i>All Activities
                    <span class="badge bg-secondary">{{ $activities->total() }}</span>
                </h5>
                @if ($search || $actionFilter !== 'all' || $typeFilter !== 'all')
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($activities as $activity)
                    <a href="{{ $activity['route'] }}" class="list-group-item list-group-item-action border-0 py-2 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle p-1"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-{{ $activity['icon'] }} text-{{ $activity['color'] }}"
                                        style="font-size: 0.875rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold me-2"
                                                style="font-size: 0.875rem;">{{ $activity['title'] }}</span>
                                            @if ($activity['action'] === 'created')
                                                <span class="badge bg-success"
                                                    style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Created</span>
                                            @elseif($activity['action'] === 'updated')
                                                <span class="badge bg-primary"
                                                    style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Updated</span>
                                            @elseif($activity['action'] === 'deleted')
                                                <span class="badge bg-danger"
                                                    style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">Deleted</span>
                                            @endif
                                            <span class="badge bg-light text-dark ms-1"
                                                style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">{{ ucfirst($activity['type']) }}</span>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ $activity['subtitle'] }} • {{ $activity['description'] }}
                                        </small>
                                    </div>
                                    <small class="text-muted"
                                        style="font-size: 0.7rem;">{{ $activity['timestamp']->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-2 mb-0">No activities found matching your filters.</p>
                        <a href="{{ route('activities.index') }}" class="btn btn-orange btn-sm mt-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
        @if ($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@endsection
