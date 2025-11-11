@extends('layouts.app')

@section('title', 'Water Services')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Water Services</li>
@endsection

@section('styles')
<style>
    #content {
        background-color: #f8f9fa !important;
        background-image: none !important;
        position: relative;
    }

    #content::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("{{ asset('assets/images/water-drops.png') }}") !important;
        background-repeat: repeat !important;
        background-size: 20px 20px !important;
        opacity: 0.2;
        pointer-events: none;
        z-index: 0;
    }

    #content > * {
        position: relative;
        z-index: 1;
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

@section('content')
    @php
        $currentUser = auth()->user();
        $canManageWater = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('water');

        $statusOptions = [
            'all' => 'All services',
            'active' => 'Active only',
            'inactive' => 'Inactive only',
        ];

        $activeSort = $sort ?? 'number';
        $activeDirection = $direction ?? 'asc';

        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->reject(fn($value, $key) => $key === 'status' && $value === 'all')
            ->mapWithKeys(fn($value, $key) => $key === 'company_id' ? ['company' => $value] : [$key => $value])
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('water.services.index', $params);
        };

        $arrowClass = function (string $column, string $direction) use ($activeSort, $activeDirection) {
            if ($activeSort !== $column) {
                return 'text-muted';
            }

            return $activeDirection === $direction ? 'text-orange' : 'text-muted';
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-droplet-fill text-orange me-2"></i>
                Water Services Directory
            </h2>
            <p class="text-muted mb-0">Track water subscriptions, latest readings, and billing status across the portfolio.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('water.bills.index') }}" class="btn btn-outline-orange">
                <i class="bi bi-receipt me-1"></i>Bills
            </a>
            <a href="{{ route('water.overview') }}" class="btn btn-outline-orange">
                <i class="bi bi-speedometer2 me-1"></i>Overview
            </a>
            @if ($canManageWater)
                <a href="{{ route('water-services.readings.bulk') }}" class="btn btn-outline-orange">
                    <i class="bi bi-cloud-upload me-1"></i>Bulk Upload
                </a>
                <a href="{{ route('water-services.deleted') }}" class="btn btn-trash">
                    <i class="bi bi-trash"></i>Deleted Services
                </a>
                <a href="{{ route('water-services.create') }}" class="btn btn-orange">
                    <i class="bi bi-plus-circle me-1"></i>Add Service
                </a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-lg mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-lg-3">
                    <label for="search" class="form-label fw-semibold">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Building, registration, owner..." value="{{ $filters['search'] }}">
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="company" class="form-label fw-semibold">Company</label>
                    <select class="form-select" id="company" name="company">
                        <option value="">All</option>
                        @foreach ($companies as $companyId => $companyName)
                            <option value="{{ $companyId }}" @selected((string) $filters['company_id'] === (string) $companyId)>{{ $companyName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="governorate" class="form-label fw-semibold">Governorate</label>
                    <select class="form-select" id="governorate" name="governorate">
                        <option value="">All</option>
                        @foreach ($governorates as $code => $label)
                            <option value="{{ $code }}" @selected($filters['governorate'] === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? 'all') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-2 text-lg-end">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        <a href="{{ route('water.services.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Apply
                        </button>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', $sort) }}">
                <input type="hidden" name="direction" value="{{ request('direction', $direction) }}">
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-clipboard-data me-2 text-orange"></i>
                        Water Service Records
                    </h5>
                    <small class="text-muted">Showing {{ number_format($waterServices->total()) }} service(s)</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center sortable" style="width: 60px;">
                                <a href="{{ $buildSortUrl('number') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>#</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('number', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('number', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('building') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Building</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('building', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('building', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('owner') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Service Details</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('owner', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('owner', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable" style="width: 220px;">
                                <a href="{{ $buildSortUrl('company') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Company</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('company', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('company', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="text-nowrap">Latest Reading</th>
                            <th class="text-nowrap">Latest Bill</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waterServices as $index => $service)
                            @php
                                $rowNumber = ($waterServices->firstItem() ?? 0) + $index;
                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    $rowNumber = $waterServices->total() - (($waterServices->currentPage() - 1) * $waterServices->perPage() + $index);
                                }

                                $companyArabic = optional($service->waterCompany)->name_ar ?? $service->company_name_ar;
                                $companyEnglish = optional($service->waterCompany)->name ?? $service->company_name;
                                $latestReading = $service->latestReading;
                                $governorate = $service->building?->site?->governorate_name_en ?? $service->building?->site?->governorate;
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $rowNumber }}</td>
                                <td>
                                    @if ($service->building)
                                        <div class="fw-semibold text-primary">
                                            <a href="{{ route('buildings.show', $service->building) }}" class="text-decoration-none">{{ $service->building->name }}</a>
                                        </div>
                                        <small class="text-muted">{{ $governorate ?? '—' }}</small>
                                    @else
                                        <span class="text-muted">No Building</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark d-flex align-items-center gap-2">
                                        {{ $service->meter_owner_name }}
                                        @unless ($service->is_active)
                                            <span class="badge bg-secondary text-white">Inactive</span>
                                        @endunless
                                    </div>
                                    <div class="text-muted small">
                                        {{ $companyEnglish }}
                                        @if ($companyArabic)
                                            <span class="d-block">{{ $companyArabic }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-1 small">
                                        <span class="badge bg-light text-muted border">Reg: {{ $service->registration_number }}</span>
                                        <span class="badge bg-light text-muted border">Iron: {{ $service->iron_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $companyEnglish }}</div>
                                    @if ($companyArabic)
                                        <div class="text-muted small">{{ $companyArabic }}</div>
                                    @endif
                                    @if (optional($service->waterCompany)?->website)
                                        <a href="{{ $service->waterCompany->website }}" target="_blank" class="small text-decoration-none">
                                            <i class="bi bi-globe"></i> Website
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestReading)
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->current_reading, 2) }}
                                            <span class="text-muted">m³</span>
                                        </div>
                                        <small class="text-muted">{{ optional($latestReading->reading_date)->format('Y-m-d') ?? 'No date' }}</small>
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
                                        <span class="badge rounded-pill fw-semibold {{ $latestReading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ $latestReading->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No bill</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('water-services.show', $service) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($canManageWater)
                                            <a href="{{ route('water-services.edit', $service) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="openDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ addslashes(trim(($companyEnglish ?? '') . ($companyArabic ? ' / ' . $companyArabic : ''))) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-droplet" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-1 fw-semibold">No water services found</p>
                                    <small>Adjust filters or add a new service to begin tracking.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($waterServices->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $waterServices->links() }}
            </div>
        @endif
    </div>

    @if ($canManageWater)
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" style="z-index: 1060;">
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
                            <strong id="deleteServiceRegistration"></strong>
                            <span class="d-block small" id="deleteServiceCompany"></span>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            <small>This action moves the service to the trash. You can restore it later from Deleted Services.</small>
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
    @endif

    <script>
        @if ($canManageWater)
            function openDeleteModal(serviceId, registrationNumber, companyName) {
                document.getElementById('deleteServiceRegistration').textContent = registrationNumber;
                document.getElementById('deleteServiceCompany').textContent = companyName || '—';
                document.getElementById('deleteForm').action = '/water-services/' + serviceId;

                const modalElement = document.getElementById('deleteModal');
                const modal = new boosted.Modal(modalElement);
                modal.show();
            }
        @endif
    </script>
@endsection
