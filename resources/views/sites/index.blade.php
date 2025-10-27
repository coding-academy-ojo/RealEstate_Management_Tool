@extends('layouts.app')

@section('title', 'Sites Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Sites</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Sites Management</h2>
        <div>
            <a href="{{ route('sites.deleted') }}" class="btn btn-trash me-2">
                <i class="bi bi-trash"></i> Deleted Sites
            </a>
            <a href="{{ route('sites.create') }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add New Site
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
            <form method="GET" action="{{ route('sites.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" class="form-control ps-5"
                                placeholder="Search by code, name..." style="border-radius: 10px;"
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="region" id="region" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['region']) ? 'selected' : '' }}>All Regions</option>
                            @foreach ($regions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ ($filters['region'] ?? '') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="governorate" id="governorate" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['governorate']) ? 'selected' : '' }}>All Governorates
                            </option>
                            @foreach ($governorates as $code => $name)
                                <option value="{{ $code }}"
                                    {{ ($filters['governorate'] ?? '') === $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="zoning" id="zoning" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['zoning']) ? 'selected' : '' }}>All Zoning Statuses
                            </option>
                            @foreach ($zoningStatuses as $zoning)
                                <option value="{{ $zoning->id }}"
                                    {{ ($filters['zoning'] ?? '') == $zoning->id ? 'selected' : '' }}>
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
                            <a href="{{ route('sites.index') }}" class="btn btn-light flex-fill"
                                style="border-radius: 10px;">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            </form>

            <div class="mt-3">
                <small class="text-muted">{{ $sites->total() }} site(s) found</small>
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

            return route('sites.index', $params);
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
                                <a href="{{ $buildSortUrl('governorate') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Region / Governorate</span>
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
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('lands') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Lands</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('lands', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('lands', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sites as $index => $site)
                            @php
                                $buildingsCount = $site->buildings_count ?? $site->buildings->count();
                                $landsCount = $site->lands_count ?? $site->lands->count();

                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    $rowNumber =
                                        $sites->total() - (($sites->currentPage() - 1) * $sites->perPage() + $index);
                                } else {
                                    $rowNumber = ($sites->firstItem() ?? 0) + $index;
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $rowNumber }}</td>
                                <td>{{ $site->code }}</td>
                                <td class="fw-semibold">{{ $site->name }}</td>
                                <td>
                                    {{ $governorates[$site->governorate] ?? $site->governorate_name_en }}
                                    <br><small
                                        class="text-muted">{{ $site->region_name ?? ($regions[$site->region] ?? '—') }}</small>
                                </td>
                                <td>{{ number_format($site->area_m2, 2) }}</td>
                                <td>{{ $buildingsCount }}</td>
                                <td>{{ $landsCount }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-outline-primary"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('sites.edit', $site) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal('{{ $site->id }}', '{{ $site->code }}', '{{ $site->name }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No sites found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($sites->hasPages())
            <div class="card-footer bg-white">
                {{ $sites->links() }}
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
                    <p class="mb-2">Are you sure you want to delete this site?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteSiteCode"></strong> - <span id="deleteSiteName"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the site to trash. You can restore it later from the Deleted Sites
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
        function openDeleteModal(siteId, siteCode, siteName) {
            document.getElementById('deleteSiteCode').textContent = siteCode;
            document.getElementById('deleteSiteName').textContent = siteName;
            document.getElementById('deleteForm').action = '/sites/' + siteId;

            // Use Boosted modal API
            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>

    <style>
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
