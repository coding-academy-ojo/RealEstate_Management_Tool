@extends('layouts.app')

@section('title', 'Electricity Services')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Electricity Services</li>
@endsection

@section('content')
    @php
        $currentUser = auth()->user();
        $canManageElectricity = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('electricity');
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Electricity Services Management</h2>
        @if ($canManageElectricity)
            <div>
                <a href="{{ route('electricity-services.deleted') }}" class="btn btn-trash me-2">
                    <i class="bi bi-trash"></i> Deleted Services
                </a>
                <a href="{{ route('electricity-services.create') }}" class="btn btn-orange">
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('electricity-services.index') }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute"
                                style="left: 12px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                            <input type="text" name="search" id="search" class="form-control ps-5"
                                placeholder="Search by subscriber, meter, company, or building..."
                                style="border-radius: 10px;" value="{{ $filters['search'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <select name="company" id="company" class="form-select" style="border-radius: 10px;">
                            <option value="" {{ empty($filters['company_id'] ?? '') ? 'selected' : '' }}>All Companies</option>
                            @foreach ($companies as $companyValue => $companyName)
                                <option value="{{ $companyValue }}"
                                    {{ (string) ($filters['company_id'] ?? '') === (string) $companyValue ? 'selected' : '' }}>
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
                            <a href="{{ route('electricity-services.index') }}" class="btn btn-light flex-fill"
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
                <small class="text-muted">{{ $electricityServices->total() }} service(s) found</small>
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

            return route('electricity-services.index', $params);
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
                            <th class="text-center sortable">
                                <a href="{{ $buildSortUrl('number') }}" class="text-decoration-none text-dark">
                                    #
                                    <span class="sort-arrows ms-1">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('number', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('number', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('building') }}" class="text-decoration-none text-dark">
                                    Building
                                    <span class="sort-arrows ms-1">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('building', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('building', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('subscriber') }}" class="text-decoration-none text-dark">
                                    Meter Info
                                    <span class="sort-arrows ms-1">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('subscriber', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('subscriber', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Latest Reading</th>
                            <th>Latest Bill</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($electricityServices as $index => $service)
                            @php
                                if ($activeSort === 'number' && $activeDirection === 'desc') {
                                    $rowNumber =
                                        $electricityServices->total() -
                                        (($electricityServices->currentPage() - 1) * $electricityServices->perPage() +
                                            $index);
                                } else {
                                    $rowNumber = ($electricityServices->firstItem() ?? 0) + $index;
                                }

                                $latestReading = $service->latestReading;
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
                                    @php
                                        // Extract subscriber name without building code
                                        $subscriberNameParts = explode(' - ', $service->subscriber_name);
                                        $cleanSubscriberName = $subscriberNameParts[0];
                                        $companyArabic = optional($service->electricityCompany)->name_ar ?? $service->company_name_ar;
                                    @endphp
                                    <div class="fw-semibold text-dark">{{ $cleanSubscriberName }}</div>
                                    <div class="text-muted small">
                                        @if ($companyArabic)
                                            {{ $companyArabic }}
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-1 small">
                                        <span class="badge bg-light text-muted border">Solar:
                                            {{ $service->has_solar_power ? 'Yes' : 'No' }}</span>
                                        <span class="badge bg-light text-muted border">Reg:
                                            {{ $service->registration_number }}</span>
                                        <span class="badge bg-light text-muted border">Meter:
                                            {{ $service->meter_number }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($latestReading)
                                        @if ($service->has_solar_power)
                                            <div class="fw-semibold">
                                                {{ number_format((float) (($latestReading->calculated_produced ?? 0) - ($latestReading->calculated_imported ?? 0)), 2) }}
                                                <span class="text-muted">kWh</span>
                                            </div>
                                        @else
                                            <div class="fw-semibold">
                                                {{ number_format((float) $latestReading->calculated_reading, 2) }}
                                                <span class="text-muted">kWh</span>
                                            </div>
                                        @endif
                                        <small class="text-muted d-block">
                                            {{ optional($latestReading->reading_date)->format('Y-m-d') ?? 'No date' }}
                                        </small>
                                    @else
                                        <span class="text-muted">No readings yet</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($latestReading && $latestReading->bill_amount !== null)
                                        <div class="fw-semibold">
                                            {{ number_format((float) $latestReading->bill_amount, 2) }}
                                            <span class="text-muted">JOD</span>
                                        </div>
                                        <span
                                            class="badge rounded-pill fw-semibold {{ $latestReading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ $latestReading->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No bills recorded</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('electricity-services.show', $service) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if ($canManageElectricity)
                                            <a href="{{ route('electricity-services.edit', $service) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                            onclick="openDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ addslashes($companyArabic ?? '') }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-lightning-charge" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No electricity services found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($electricityServices->hasPages())
            <div class="card-footer bg-white">
                {{ $electricityServices->links() }}
            </div>
        @endif
    </div>

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
                    <p class="mb-2">Are you sure you want to delete this electricity service?</p>
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
