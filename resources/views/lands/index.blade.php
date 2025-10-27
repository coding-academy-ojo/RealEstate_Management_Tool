@extends('layouts.app')

@section('title', 'Lands Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Lands</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Lands Management</h2>
        <div>
            <a href="{{ route('lands.deleted') }}" class="btn btn-trash me-2">
                <i class="bi bi-trash"></i> Deleted Lands
            </a>
            <a href="{{ route('lands.create') }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add New Land
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('lands.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" class="form-control ps-5"
                                placeholder="Search by plot key, site name..." style="border-radius: 10px;"
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="governorate" id="governorate" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['governorate']) ? 'selected' : '' }}>All Governorates
                            </option>
                            @foreach ($governorates as $governorate)
                                <option value="{{ $governorate }}"
                                    {{ ($filters['governorate'] ?? '') === $governorate ? 'selected' : '' }}>
                                    {{ $governorate }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="zoning" id="zoning" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['zoning']) ? 'selected' : '' }}>All Zoning Statuses
                            </option>
                            @foreach ($zoningStatuses as $zoning)
                                <option value="{{ $zoning->name_ar }}"
                                    {{ ($filters['zoning'] ?? '') === $zoning->name_ar ? 'selected' : '' }}>
                                    {{ $zoning->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-fill" style="border-radius: 10px;">
                                <i class="bi bi-funnel me-1"></i> Apply
                            </button>
                            <a href="{{ route('lands.index') }}" class="btn btn-light flex-fill"
                                style="border-radius: 10px;">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-2">
                        <input type="text" name="directorate" id="directorate" class="form-control"
                            placeholder="Directorate..." style="border-radius: 10px;"
                            value="{{ $filters['directorate'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="village" id="village" class="form-control" placeholder="Village..."
                            style="border-radius: 10px;" value="{{ $filters['village'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="basin" id="basin" class="form-control" placeholder="Basin..."
                            style="border-radius: 10px;" value="{{ $filters['basin'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="neighborhood" id="neighborhood" class="form-control"
                            placeholder="Neighborhood..." style="border-radius: 10px;"
                            value="{{ $filters['neighborhood'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="plot_number" id="plot_number" class="form-control"
                            placeholder="Plot Number..." style="border-radius: 10px;"
                            value="{{ $filters['plot_number'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="region" id="region" class="form-control" placeholder="Region..."
                            style="border-radius: 10px;" value="{{ $filters['region'] ?? '' }}">
                    </div>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            </form>

            <div class="mt-3">
                <small class="text-muted">{{ $lands->total() }} land(s) found</small>
            </div>
        </div>
    </div>

    @php
        $activeSort = $sort ?? 'number';
        $activeDirection = $direction ?? 'desc';
        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('lands.index', $params);
        };

        $arrowClass = function (string $column, string $direction) use ($activeSort, $activeDirection) {
            if ($activeSort !== $column) {
                return 'text-muted';
            }

            return $activeDirection === $direction ? 'text-primary' : 'text-muted';
        };
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="sortable" style="width: 60px;">
                                <a href="{{ $buildSortUrl('number') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>No.</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('number', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('number', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('plot_key') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Plot Key</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('plot_key', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('plot_key', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('site') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Site</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('site', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('site', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('governorate') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Governorate</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('governorate', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('governorate', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('area') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Area (m²)</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('area', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('area', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('buildings') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Buildings</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('buildings', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('buildings', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lands as $index => $land)
                            @php
                                $buildingsCount = $land->buildings_count ?? $land->buildings->count();

                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    $rowNumber =
                                        $lands->total() - (($lands->currentPage() - 1) * $lands->perPage() + $index);
                                } else {
                                    $rowNumber = ($lands->firstItem() ?? 0) + $index;
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $rowNumber }}</td>
                                <td>{{ $land->plot_key }}</td>
                                <td>
                                    <a href="{{ route('sites.show', $land->site) }}"
                                        class="text-primary text-decoration-none site-link">
                                        {{ $land->site->name ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $land->governorate }}</td>
                                <td>{{ number_format($land->area_m2, 2) }}</td>
                                <td>{{ $buildingsCount }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('lands.show', $land) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('lands.edit', $land) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal({{ $land->id }}, '{{ $land->plot_key }}', '{{ $land->site ? $land->site->code . ' - ' . $land->site->name : 'Site deleted' }}', '{{ $land->basin }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No lands found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($lands->hasPages())
            <div class="card-footer bg-white">
                {{ $lands->links() }}
            </div>
        @endif
    </div>



    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete this land parcel?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteLandPlot"></strong>
                        <span class="d-block"><small id="deleteLandSite" class="text-muted"></small></span>
                        <span class="d-block"><small id="deleteLandBasin" class="text-muted"></small></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the land to trash. You can restore it later from the Deleted Lands
                            page.</small>
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(landId, plotKey, siteInfo, basin) {
            try {
                document.getElementById('deleteLandPlot').textContent = plotKey || 'Unknown plot key';
                document.getElementById('deleteLandSite').textContent = siteInfo || 'No site information';
                const basinText = basin ? `Basin: ${basin}` : 'No basin information';
                document.getElementById('deleteLandBasin').textContent = basinText;
                document.getElementById('deleteForm').action = '/lands/' + landId;

                const modalElement = document.getElementById('deleteModal');
                if (!modalElement) {
                    console.error('Delete modal element not found');
                    return;
                }

                // Use Boosted modal API
                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening delete modal:', error);
                alert('Error opening modal. Please refresh the page and try again.');
            }
        }
    </script>

    <style>
        .site-link:hover {
            font-weight: bold;
        }

        .sortable .sort-arrows {
            display: inline-flex;
            flex-direction: column;
            line-height: 1;
        }

        .sortable .sort-arrows .bi {
            font-size: 0.75rem;
        }
    </style>
@endsection
