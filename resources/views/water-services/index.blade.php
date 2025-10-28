@extends('layouts.app')

@section('title', 'Water Services')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Water Services</li>
@endsection

@section('content')
    @php
        $currentUser = auth()->user();
        $canManageWater = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('water');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Water Services Management</h2>
        @if ($canManageWater)
            <div>
                <a href="{{ route('water-services.deleted') }}" class="btn btn-trash me-2">
                    <i class="bi bi-trash"></i> Deleted Services
                </a>
                <a href="{{ route('water-services.create') }}" class="btn btn-orange">
                    <i class="bi bi-plus-circle me-1"></i> Add New Service
                </a>
            </div>
        @endif
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
            <form method="GET" action="{{ route('water-services.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" class="form-control ps-5"
                                placeholder="Search by registration #, iron #, company, building..."
                                style="border-radius: 10px;" value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <select name="company" id="company" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['company']) ? 'selected' : '' }}>All Companies</option>
                            @foreach ($companies as $companyValue => $companyName)
                                <option value="{{ $companyValue }}"
                                    {{ ($filters['company'] ?? '') === $companyValue ? 'selected' : '' }}>
                                    {{ $companyName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-orange flex-fill" style="border-radius: 10px;">
                                <i class="bi bi-funnel me-1"></i> Apply
                            </button>
                            <a href="{{ route('water-services.index') }}" class="btn btn-light flex-fill"
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
                <small class="text-muted">{{ $waterServices->total() }} service(s) found</small>
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

            return route('water-services.index', $params);
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
                                <a href="{{ $buildSortUrl('building') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Building</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('building', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('building', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('company') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Meter Info</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('company', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('company', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Latest Reading</th>
                            <th>Latest Bill</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waterServices as $index => $service)
                            @php
                                // LIFO numbering: newest record gets lowest number
                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    // When sorting desc, reverse the numbering
                                    $rowNumber =
                                        $waterServices->total() -
                                        (($waterServices->currentPage() - 1) * $waterServices->perPage() + $index);
                                } else {
                                    // Default asc: normal numbering (1, 2, 3...)
                                    $rowNumber = ($waterServices->firstItem() ?? 0) + $index;
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $rowNumber }}</td>
                                <td>
                                    @if ($service->building)
                                        <a href="{{ route('buildings.show', $service->building) }}"
                                            class="text-decoration-none text-primary fw-semibold">
                                            {{ $service->building->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No Building</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $service->meter_owner_name }}</div>
                                    <div class="text-muted small">{{ $service->company_name }}</div>
                                    <div class="d-flex flex-wrap gap-2 mt-1 small">
                                        <span class="badge bg-light text-muted border">Reg: {{ $service->registration_number }}</span>
                                        <span class="badge bg-light text-muted border">Iron: {{ $service->iron_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                @php
                                    $latestReading = $service->latestReading;
                                @endphp
                                <td>
                                    @if ($latestReading)
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->current_reading, 2) }}
                                            <span class="text-muted">m³</span>
                                        </div>
                                        <small class="text-muted">
                                            {{ optional($latestReading->reading_date)->format('Y-m-d') ?? 'No date' }}
                                        </small>
                                    @else
                                        <span class="text-muted">No readings</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestReading && !is_null($latestReading->bill_amount))
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->bill_amount, 2) }}
                                            <span class="text-muted">JOD</span>
                                        </div>
                                        <span
                                            class="badge rounded-pill fw-semibold {{ $latestReading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ $latestReading->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No bill</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('water-services.show', $service) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('water-services.edit', $service) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ $service->company_name }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-droplet" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No water services found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($waterServices->hasPages())
            <div class="card-footer bg-white">
                {{ $waterServices->links() }}
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
                    <p class="mb-2">Are you sure you want to delete this water service?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteServiceRegistration"></strong> - <span id="deleteServiceCompany"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the service to trash. You can restore it later from the Deleted
                            Services page.</small>
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
        function openDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('deleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('deleteServiceCompany').textContent = companyName;
            document.getElementById('deleteForm').action = '/water-services/' + serviceId;

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
