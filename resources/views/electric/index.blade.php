@extends('layouts.app')

@section('title', 'Electricity Services')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Electricity Services</li>
@endsection

@section('content')
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
            background-image: url("{{ asset('assets/images/energie.png') }}") !important;
            background-repeat: repeat !important;
            background-size: 22px 22px !important;
            opacity: 0.18;
            pointer-events: none;
            z-index: 0;
        }

        #content>* {
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

    @php
        $currentUser = auth()->user();
        $canManageElectricity = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('electricity');
        $statusOptions = [
            'all' => 'All statuses',
            'active' => 'Active only',
            'inactive' => 'Inactive only',
        ];
        $solarOptions = [
            'all' => 'Solar & non-solar',
            'with' => 'Solar services',
            'without' => 'Non-solar only',
        ];

        $activeSort = $sort ?? 'number';
        $activeDirection = $direction ?? 'desc';
        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->reject(fn($value, $key) => in_array($key, ['status', 'solar']) && $value === 'all')
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('electricity-services.index', $params);
        };

        $arrowClass = function (string $column, string $direction) use ($activeSort, $activeDirection) {
            if ($activeSort !== $column) {
                return 'text-muted';
            }

            return $activeDirection === $direction ? 'text-warning' : 'text-muted';
        };
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                Electricity Services Management
                <small class="text-muted fs-6 fw-normal" style="font-family: 'Segoe UI', Tahoma, sans-serif;">إدارة خدمات الكهرباء</small>
            </h2>
            <p class="text-muted mb-0">
                Track calculated electricity meters, solar status, and company assignments in one place.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('electricity.bills.index') }}" class="btn btn-outline-orange">
                <i class="bi bi-receipt me-1"></i> Bills
            </a>
            <a href="{{ route('electricity.overview') }}" class="btn btn-outline-orange">
                <i class="bi bi-speedometer2 me-1"></i> Overview
            </a>
            @if ($canManageElectricity)
                <a href="{{ route('electricity-services.deleted') }}" class="btn btn-trash">
                    <i class="bi bi-trash"></i> Deleted Services
                </a>
                <a href="{{ route('electricity-services.create') }}" class="btn btn-orange">
                    <i class="bi bi-plus-circle me-1"></i> Add Service
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
                    <input type="text" class="form-control" id="search" name="search" placeholder="Service, building, meter, subscriber..." value="{{ $filters['search'] }}">
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="company" class="form-label fw-semibold">Company</label>
                    <select class="form-select" id="company" name="company">
                        <option value="">All</option>
                        @foreach ($companies as $companyId => $companyName)
                            <option value="{{ $companyId }}" @selected((string) $filters['company_id'] === (string) $companyId)>{{ $companyName }}</option>
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
                <div class="col-12 col-sm-6 col-lg-2">
                    <label for="solar" class="form-label fw-semibold">Solar</label>
                    <select class="form-select" id="solar" name="solar">
                        @foreach ($solarOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['solar'] ?? 'all') === $value)>{{ $label }}</option>
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
                <div class="col-12 col-lg-3 text-lg-end">
                    <div class="d-flex flex-wrap justify-content-lg-end gap-2">
                        <a href="{{ route('electricity-services.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', $sort) }}">
                <input type="hidden" name="direction" value="{{ request('direction', $direction) }}">
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-clipboard-data me-2 text-warning"></i>
                        Electricity Services Directory
                    </h5>
                    <small class="text-muted">Showing {{ number_format($electricityServices->total()) }} service(s)</small>
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
                                <a href="{{ $buildSortUrl('subscriber') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Service Details</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('subscriber', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('subscriber', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('company') }}" class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Company</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('company', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('company', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Latest Reading</th>
                            <th class="text-center">Disconnections</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($electricityServices as $index => $service)
                            @php
                                $rowNumber = ($electricityServices->firstItem() ?? 0) + $index;
                                $latestReading = $service->latestReading;
                                $companyArabic = optional($service->electricityCompany)->name_ar ?? $service->company_name_ar;
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
                                    <div class="fw-semibold text-dark">{{ $service->subscriber_name }}</div>
                                    <div class="text-muted small">Reg: {{ $service->registration_number }} | Meter: {{ $service->meter_number }}</div>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <span class="badge {{ $service->has_solar_power ? 'bg-warning text-dark' : 'bg-light text-muted border' }}">
                                            <i class="bi bi-sun me-1"></i>{{ $service->has_solar_power ? 'Solar' : 'Grid only' }}
                                        </span>
                                        <span class="badge {{ $service->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $service->electricityCompany?->name ?? ($service->company_name ?? 'N/A') }}</div>
                                    @if ($companyArabic)
                                        <div class="text-muted small">{{ $companyArabic }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestReading)
                                        <div class="fw-semibold text-warning">{{ number_format((float) ($latestReading->consumption_value ?? 0), 2) }} kWh</div>
                                        <small class="text-muted">On {{ optional($latestReading->reading_date)->format('Y-m-d') ?? 'N/A' }}</small>
                                        <div class="text-muted small">Bill: {{ $latestReading->bill_amount ? number_format((float) $latestReading->bill_amount, 2) . ' JOD' : '—' }}</div>
                                    @else
                                        <span class="text-muted">No readings</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($service->open_disconnections_count > 0)
                                        <span class="badge bg-danger">
                                            {{ $service->open_disconnections_count }} open
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">None</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('electricity-services.show', $service) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($canManageElectricity)
                                            <a href="{{ route('electricity-services.edit', $service) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="openDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ addslashes($service->electricityCompany?->name ?? $service->company_name ?? '') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-lightning-charge" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-1 fw-semibold">No electricity services found</p>
                                    <small>Adjust filters or add a new service to get started.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($electricityServices->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $electricityServices->links() }}
            </div>
        @endif
    </div>

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
                    <p class="mb-2">Are you sure you want to delete this electricity service?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteServiceRegistration"></strong>
                        <span class="d-block small" id="deleteServiceCompany"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This will move the service to trash. You can restore it later from Deleted Services.</small>
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
            document.getElementById('deleteServiceCompany').textContent = companyName || '—';
            document.getElementById('deleteForm').action = '/electricity-services/' + serviceId;

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
