@extends('layouts.app')

@section('title', 'Re-Innovations')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Re-Innovations</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Re-Innovations Management</h2>
        <div>
            <a href="{{ route('re-innovations.deleted') }}" class="btn btn-trash me-2">
                <i class="bi bi-trash"></i> Deleted Re-Innovations
            </a>
            <a href="{{ route('re-innovations.create') }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add New Re-Innovation
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
            <form method="GET" action="{{ route('re-innovations.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" class="form-control ps-5"
                                placeholder="Search by name, description..." style="border-radius: 10px;"
                                value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <select name="type" id="type" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['type']) ? 'selected' : '' }}>All Types</option>
                            @foreach ($types as $typeValue => $typeName)
                                <option value="{{ $typeValue }}"
                                    {{ ($filters['type'] ?? '') === $typeValue ? 'selected' : '' }}>
                                    {{ $typeName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-fill" style="border-radius: 10px;">
                                <i class="bi bi-funnel me-1"></i> Apply
                            </button>
                            <a href="{{ route('re-innovations.index') }}" class="btn btn-light flex-fill"
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
                <small class="text-muted">{{ $reInnovations->total() }} re-innovation(s) found</small>
            </div>
        </div>
    </div>

    @php
        $activeSort = $sort ?? 'number';
        $activeDirection = $direction ?? 'asc';
        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('re-innovations.index', $params);
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
                                <a href="{{ $buildSortUrl('type') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Type / Related To</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('type', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('type', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('date') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Date</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('date', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('date', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('cost') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Cost (JOD)</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('cost', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('cost', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reInnovations as $index => $innovation)
                            @php
                                // LIFO numbering: newest record gets lowest number
                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    // When sorting desc, reverse the numbering
                                    $rowNumber =
                                        $reInnovations->total() -
                                        (($reInnovations->currentPage() - 1) * $reInnovations->perPage() + $index);
                                } else {
                                    // Default asc: normal numbering (1, 2, 3...)
                                    $rowNumber = ($reInnovations->firstItem() ?? 0) + $index;
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $rowNumber }}</td>
                                <td>
                                    <div class="fw-semibold">{{ class_basename($innovation->innovatable_type) }}</div>
                                    @if ($innovation->innovatable)
                                        @php
                                            $routeName =
                                                strtolower(
                                                    str_replace('App\\Models\\', '', $innovation->innovatable_type),
                                                ) . 's.show';
                                        @endphp
                                        <a href="{{ route($routeName, $innovation->innovatable) }}"
                                            class="text-decoration-none text-primary">
                                            {{ $innovation->innovatable->name ?? ($innovation->innovatable->code ?? 'N/A') }}
                                        </a>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $innovation->date ? $innovation->date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ number_format($innovation->cost, 2) }}</td>
                                <td>
                                    <span class="text-muted small">{{ Str::limit($innovation->description, 50) }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('re-innovations.show', $innovation) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('re-innovations.edit', $innovation) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal('{{ $innovation->id }}', '{{ $innovation->name }}', '{{ class_basename($innovation->innovatable_type) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-lightbulb" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No re-innovations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($reInnovations->hasPages())
            <div class="card-footer bg-white">
                {{ $reInnovations->links() }}
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
                    <p class="mb-2">Are you sure you want to delete this re-innovation?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteInnovationName"></strong> (<span id="deleteInnovationType"></span>)
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the re-innovation to trash. You can restore it later from the
                            Deleted Re-Innovations page.</small>
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
        function openDeleteModal(innovationId, innovationName, innovationType) {
            document.getElementById('deleteInnovationName').textContent = innovationName;
            document.getElementById('deleteInnovationType').textContent = innovationType;
            document.getElementById('deleteForm').action = '/re-innovations/' + innovationId;

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
