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
                        <option value="water_reading" {{ $typeFilter === 'water_reading' ? 'selected' : '' }}>Water
                            Readings</option>
                        <option value="water_company" {{ $typeFilter === 'water_company' ? 'selected' : '' }}>Water
                            Companies</option>
                        <option value="electricity" {{ $typeFilter === 'electricity' ? 'selected' : '' }}>Electricity
                            Services</option>
                        <option value="electric_reading" {{ $typeFilter === 'electric_reading' ? 'selected' : '' }}>
                            Electric Readings</option>
                        <option value="electricity_company" {{ $typeFilter === 'electricity_company' ? 'selected' : '' }}>
                            Electricity Companies</option>
                        <option value="disconnection" {{ $typeFilter === 'disconnection' ? 'selected' : '' }}>Service
                            Disconnections</option>
                        <option value="innovation" {{ $typeFilter === 'innovation' ? 'selected' : '' }}>Renovations
                        </option>
                        <option value="image" {{ $typeFilter === 'image' ? 'selected' : '' }}>Images</option>
                        <option value="zoning" {{ $typeFilter === 'zoning' ? 'selected' : '' }}>Zoning Status</option>
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
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-3 py-3" style="width: 5%;">
                                <i class="bi bi-activity text-muted"></i>
                            </th>
                            <th class="py-3" style="width: 30%;">Activity</th>
                            <th class="py-3" style="width: 15%;">Entity Type</th>
                            <th class="py-3" style="width: 20%;">User</th>
                            <th class="py-3" style="width: 15%;">Action</th>
                            <th class="py-3 text-end pe-3" style="width: 15%;">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr class="activity-row" style="cursor: pointer;"
                                onclick="window.location='{{ $activity['route'] }}'">
                                <td class="px-3">
                                    <div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle p-2"
                                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-{{ $activity['icon'] }} text-{{ $activity['color'] }}"
                                            style="font-size: 1rem;"></i>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $activity['title'] }}</div>
                                </td>
                                <td>
                                    <span class="badge"
                                        style="font-size: 0.75rem; padding: 0.25rem 0.6rem;
                                        @if ($activity['type'] === 'site') background-color: #ff7900;
                                        @elseif($activity['type'] === 'building') background-color: #6c757d;
                                        @elseif($activity['type'] === 'land') background-color: #198754;
                                        @elseif(str_contains($activity['type'], 'water')) background-color: #0d6efd;
                                        @elseif(str_contains($activity['type'], 'electric')) background-color: #ffc107; color: #000;
                                        @elseif($activity['type'] === 'innovation') background-color: #dc3545;
                                        @elseif($activity['type'] === 'image') background-color: #6f42c1;
                                        @elseif($activity['type'] === 'zoning') background-color: #20c997;
                                        @else background-color: #adb5bd; @endif">
                                        <i class="bi bi-{{ $activity['icon'] }} me-1"></i>
                                        @if ($activity['type'] === 'site')
                                            Site
                                        @elseif($activity['type'] === 'building')
                                            Building
                                        @elseif($activity['type'] === 'land')
                                            Land
                                        @elseif($activity['type'] === 'water')
                                            Water Service
                                        @elseif($activity['type'] === 'water_reading')
                                            Water Reading
                                        @elseif($activity['type'] === 'water_company')
                                            Water Company
                                        @elseif($activity['type'] === 'electricity')
                                            Electricity Service
                                        @elseif($activity['type'] === 'electric_reading')
                                            Electric Reading
                                        @elseif($activity['type'] === 'electricity_company')
                                            Electric Company
                                        @elseif($activity['type'] === 'disconnection')
                                            Disconnection
                                        @elseif($activity['type'] === 'innovation')
                                            Renovation
                                        @elseif($activity['type'] === 'image')
                                            Image
                                        @elseif($activity['type'] === 'zoning')
                                            Zoning
                                        @else
                                            {{ ucfirst($activity['type']) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle me-2"
                                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-person-fill text-primary" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size: 0.875rem;">
                                                {{ $activity['subtitle'] }}</div>
                                            @if (isset($activity['user_role']))
                                                <small class="text-muted"
                                                    style="font-size: 0.7rem;">{{ $activity['user_role'] }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($activity['action'] === 'created')
                                        <span class="badge bg-success" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                                            <i class="bi bi-plus-circle me-1"></i>Created
                                        </span>
                                    @elseif($activity['action'] === 'updated')
                                        <span class="badge bg-primary"
                                            style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                                            <i class="bi bi-pencil me-1"></i>Updated
                                        </span>
                                    @elseif($activity['action'] === 'deleted')
                                        <span class="badge bg-danger"
                                            style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                                            <i class="bi bi-trash me-1"></i>Deleted
                                        </span>
                                    @elseif($activity['action'] === 'restored')
                                        <span class="badge bg-info" style="font-size: 0.75rem; padding: 0.25rem 0.6rem;">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restored
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="text-dark fw-semibold" style="font-size: 0.875rem;">
                                        {{ $activity['timestamp']->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        {{ $activity['timestamp']->format('h:i A') }}
                                        <span class="text-muted">({{ $activity['timestamp']->diffForHumans() }})</span>
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No activities found matching your filters.</p>
                                    <a href="{{ route('activities.index') }}" class="btn btn-orange btn-sm mt-3">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($activities->hasPages())
            <div class="card-footer bg-white">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

    <style>
        .activity-row {
            transition: all 0.2s ease;
        }

        .activity-row:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .table> :not(caption)>*>* {
            border-bottom-width: 1px;
        }
    </style>
@endsection
