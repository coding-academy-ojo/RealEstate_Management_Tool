@extends('layouts.app')

@section('title', 'Buildings Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Buildings</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Buildings Management</h2>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('buildings.deleted') }}" class="btn btn-trash">
                <i class="bi bi-trash"></i> Deleted Buildings
            </a>
            <a href="{{ route('buildings.create') }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add New Building
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('buildings.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute"
                            style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="text" id="search" name="search" class="form-control ps-5"
                            placeholder="Search by code, name, site..." style="border-radius: 10px;"
                            value="{{ $filters['search'] ?? '' }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select id="permit" name="permit" class="form-select" style="border-radius: 10px;">
                        <option value="" {{ empty($filters['permit']) ? 'selected' : '' }}>All Permits</option>
                        <option value="building" {{ ($filters['permit'] ?? '') === 'building' ? 'selected' : '' }}>Has
                            Building Permit</option>
                        <option value="occupancy" {{ ($filters['permit'] ?? '') === 'occupancy' ? 'selected' : '' }}>Has
                            Occupancy Permit</option>
                        <option value="profession" {{ ($filters['permit'] ?? '') === 'profession' ? 'selected' : '' }}>Has
                            Profession Permit</option>
                        <option value="no-permits" {{ ($filters['permit'] ?? '') === 'no-permits' ? 'selected' : '' }}>No
                            Permits</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="area" name="area" class="form-select" style="border-radius: 10px;">
                        <option value="" {{ empty($filters['area']) ? 'selected' : '' }}>All Areas</option>
                        <option value="0-500" {{ ($filters['area'] ?? '') === '0-500' ? 'selected' : '' }}>0 - 500 m²
                        </option>
                        <option value="500-1000" {{ ($filters['area'] ?? '') === '500-1000' ? 'selected' : '' }}>500 -
                            1,000 m²</option>
                        <option value="1000-2000" {{ ($filters['area'] ?? '') === '1000-2000' ? 'selected' : '' }}>1,000 -
                            2,000 m²</option>
                        <option value="2000+" {{ ($filters['area'] ?? '') === '2000+' ? 'selected' : '' }}>2,000+ m²
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-orange flex-fill" style="border-radius: 10px;">
                            <i class="bi bi-funnel me-1"></i> Apply
                        </button>
                        <a href="{{ route('buildings.index') }}" class="btn btn-light flex-fill"
                            style="border-radius: 10px;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            </form>

            <div class="mt-3">
                <small class="text-muted">{{ $buildings->total() }} building(s) found</small>
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

            return route('buildings.index', $params);
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
                                <a href="{{ $buildSortUrl('code') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Code</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('code', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('code', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('name') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Name</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('name', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('name', 'desc') }}"></i>
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
                                <a href="{{ $buildSortUrl('services') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Services</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('services', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('services', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buildings as $index => $building)
                            @php
                                $waterCount = $building->water_services_count ?? $building->waterServices->count();
                                $electricityCount =
                                    $building->electricity_services_count ?? $building->electricityServices->count();

                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    $rowNumber =
                                        $buildings->total() -
                                        (($buildings->currentPage() - 1) * $buildings->perPage() + $index);
                                } else {
                                    $rowNumber = ($buildings->firstItem() ?? 0) + $index;
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $rowNumber }}</td>
                                <td>{{ $building->code }}</td>
                                <td class="fw-semibold">{{ $building->name }}</td>
                                <td>
                                    @if ($building->site && !$building->site->trashed())
                                        <a href="{{ route('sites.show', $building->site) }}"
                                            class="text-primary text-decoration-none site-link">
                                            {{ $building->site->name }}
                                        </a>
                                        <div class="text-muted small">{{ $building->site->code }}</div>
                                    @elseif ($building->site && $building->site->trashed())
                                        <span class="badge bg-secondary">Site in trash</span>
                                        <div class="text-muted small">{{ $building->site->name }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ number_format($building->area_m2, 2) }}</td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bi bi-droplet text-info"></i> {{ $waterCount }}
                                        <i class="bi bi-lightning text-warning ms-2"></i> {{ $electricityCount }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('buildings.show', $building) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('buildings.edit', $building) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal({{ $building->id }}, {{ json_encode($building->code) }}, {{ json_encode($building->name) }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No buildings found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($buildings->hasPages())
            <div class="card-footer bg-white">
                {{ $buildings->links() }}
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
                    <p class="mb-2">Are you sure you want to delete this building?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteBuildingCode"></strong> - <span id="deleteBuildingName"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the building to the trash. You can restore it later from the Deleted
                            Buildings page.</small>
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
        function openDeleteModal(buildingId, buildingCode, buildingName) {
            try {
                document.getElementById('deleteBuildingCode').textContent = buildingCode || '';
                document.getElementById('deleteBuildingName').textContent = buildingName || '';
                document.getElementById('deleteForm').action = '/buildings/' + buildingId;

                const modalElement = document.getElementById('deleteModal');
                if (!modalElement) {
                    console.error('Delete modal element not found');
                    return;
                }

                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening delete modal:', error);
                alert('Unable to open the delete confirmation. Please refresh and try again.');
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
