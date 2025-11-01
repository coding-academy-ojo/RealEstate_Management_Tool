@extends('layouts.app')

@section('title', 'Electricity Bills')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity.services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Electricity Bills</li>
@endsection

@section('content')
    <style>
        #content {
            background-color: #f8f9fa !important;
            background-image: none !important;
            position: relative;
            z-index: auto !important;
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
            z-index: auto;
        }

        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.12) !important;
        }

        .metric-card .card-body {
            padding: 0.85rem 1rem !important;
        }

        .metric-card .card-footer {
            display: none !important;
        }

        .metric-card .metric-card-title {
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem !important;
        }

        .metric-card .metric-card-value {
            font-size: 1.35rem;
            line-height: 1.2;
        }

        .metric-card .metric-card-subtitle {
            font-size: 0.58rem;
            margin-top: 0.15rem !important;
        }

        .metric-card .icon-wrapper {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    @php
        $statusOptions = [
            'all' => 'All statuses',
            'paid' => 'Paid',
            'unpaid' => 'Unpaid',
        ];
        $solarOptions = [
            'all' => 'Solar & non-solar',
            'with' => 'With solar',
            'without' => 'Without solar',
        ];
        $currentUser = auth()->user();
        $canManageElectricity = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('electricity');
        $filteredCount = $readings->getCollection()->count();
        $filteredUnpaidCount = $readings->getCollection()->where('is_paid', false)->count();
        $filteredPaidCount = $readings->getCollection()->where('is_paid', true)->count();

        $cards = [
            [
                'title' => 'Outstanding Balance',
                'title_ar' => 'إجمالي المبالغ المستحقة',
                'value' => number_format($summary['total_outstanding'] ?? 0, 2),
                'suffix' => 'JOD',
                'icon' => 'bi-cash-stack',
                'color' => 'text-danger',
                'meta' => 'Filtered: ' . number_format($filteredTotals['outstanding'] ?? 0, 2) . ' JOD',
            ],
            [
                'title' => 'Unpaid Bills',
                'title_ar' => 'فواتير غير مدفوعة',
                'value' => number_format($summary['unpaid_count'] ?? 0),
                'icon' => 'bi-exclamation-triangle-fill',
                'color' => 'text-warning',
                'meta' => 'Filtered count: ' . number_format($filteredUnpaidCount),
            ],
            [
                'title' => 'Paid Bills',
                'title_ar' => 'فواتير مدفوعة',
                'value' => number_format($summary['paid_count'] ?? 0),
                'icon' => 'bi-check-circle-fill',
                'color' => 'text-success',
                'meta' => 'Filtered total: ' . number_format($filteredTotals['paid'] ?? 0, 2) . ' JOD',
            ],
            [
                'title' => 'Services Tracked',
                'title_ar' => 'الخدمات المتابعة',
                'value' => number_format($summary['unique_services'] ?? 0),
                'icon' => 'bi-lightning',
                'color' => 'text-info',
                'meta' => 'Total readings: ' . number_format($summary['total_readings'] ?? 0),
            ],
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-receipt-cutoff text-warning me-2"></i>
                Electricity Bills
                <small class="text-muted fs-6 fw-normal" style="font-family: 'Segoe UI', Tahoma, sans-serif;">
                    لوحة الفواتير
                </small>
            </h2>
            <p class="text-muted mb-0">
                Centralized view for every calculated electricity reading, status, and bill document
                <span class="text-muted small d-block mt-1">عرض مركزي للقراءات المحسوبة والفواتير</span>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('electricity.services.index') }}" class="btn btn-orange">
                <i class="bi bi-list-ul me-1"></i> All Services
            </a>
            <a href="{{ route('electricity.overview') }}" class="btn btn-outline-orange">
                <i class="bi bi-speedometer2 me-1"></i> Overview
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        @foreach ($cards as $card)
            <div class="col">
                <div class="card card-hover metric-card border-0 shadow-lg h-100"
                    style="background-color: #111827 !important;">
                    <div class="card-body text-white">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-white bg-opacity-25 rounded-circle icon-wrapper">
                                    <i class="bi {{ $card['icon'] }} {{ $card['color'] }}" style="font-size: 1rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-uppercase fw-bold metric-card-title">{{ $card['title'] }}</h6>
                                <h3 class="mb-0 text-white fw-bold metric-card-value">
                                    {{ $card['value'] }}
                                    @if (!empty($card['suffix']))
                                        <small class="text-white-50"
                                            style="font-size: 0.65rem;">{{ $card['suffix'] }}</small>
                                    @endif
                                </h3>
                                <div class="text-white-50 metric-card-subtitle"
                                    style="font-family: 'Segoe UI', sans-serif;">
                                    {{ $card['title_ar'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-black bg-opacity-10 border-0 py-2 px-3">
                        <small class="text-white-50" style="font-size: 0.7rem;">{{ $card['meta'] }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-lg mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="search" class="form-label fw-semibold">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="Service, building, notes, registration..." value="{{ $filters['search'] }}">
                </div>
                <div class="col-12 col-md-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="company" class="form-label fw-semibold">Company</label>
                    <select class="form-select" id="company" name="company">
                        <option value="">All</option>
                        @foreach ($companies as $companyId => $companyName)
                            <option value="{{ $companyId }}" @selected((string) $filters['company_id'] === (string) $companyId)>
                                {{ $companyName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label for="solar" class="form-label fw-semibold">Solar</label>
                    <select class="form-select" id="solar" name="solar">
                        @foreach ($solarOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['solar'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label for="governorate" class="form-label fw-semibold">Governorate</label>
                    <select class="form-select" id="governorate" name="governorate">
                        <option value="">All governorates</option>
                        @foreach ($governorates as $code => $label)
                            <option value="{{ $code }}" @selected($filters['governorate'] === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Date range</label>
                    <div class="d-flex gap-2">
                        <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}">
                        <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Amount min</label>
                    <input type="number" step="0.01" class="form-control" name="amount_min"
                        value="{{ $filters['amount_min'] }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">Amount max</label>
                    <input type="number" step="0.01" class="form-control" name="amount_max"
                        value="{{ $filters['amount_max'] }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i> Apply
                        </button>
                        <a href="{{ route('electricity.bills.index') }}" class="btn btn-light flex-fill">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', $sort) }}">
                <input type="hidden" name="direction" value="{{ request('direction', $direction) }}">
            </form>
        </div>
    </div>

    @php
        $activeSort = request('sort', 'date');
        $activeDirection = request('direction', 'desc');
        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->reject(fn($value, $key) => in_array($key, ['status', 'solar']) && $value === 'all')
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('electricity.bills.index', $params);
        };

        $arrowClass = function (string $column, string $direction) use ($activeSort, $activeDirection) {
            if ($activeSort !== $column) {
                return 'text-muted';
            }

            return $activeDirection === $direction ? 'text-primary' : 'text-muted';
        };
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-clipboard-data me-2 text-warning"></i>
                        Calculated Electricity Readings
                    </h5>
                    <small class="text-muted">
                        Showing {{ number_format($filteredCount) }} entries ({{ number_format($filteredUnpaidCount) }}
                        unpaid, {{ number_format($filteredPaidCount) }} paid)
                    </small>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                    Total Consumption: {{ number_format($summary['total_consumption'] ?? 0, 2) }} kWh
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;" class="sortable">
                                <a href="{{ $buildSortUrl('number') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>#</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('number', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('number', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('service') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Service</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('service', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('service', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="sortable">
                                <a href="{{ $buildSortUrl('date') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Reading Date</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('date', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('date', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="text-end sortable">
                                <a href="{{ $buildSortUrl('consumption') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Consumption (kWh)</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('consumption', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('consumption', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th class="text-end sortable">
                                <a href="{{ $buildSortUrl('bill') }}"
                                    class="d-flex align-items-center justify-content-between text-decoration-none text-dark">
                                    <span>Bill (JOD)</span>
                                    <span class="sort-arrows">
                                        <i class="bi bi-caret-up-fill {{ $arrowClass('bill', 'asc') }}"></i>
                                        <i class="bi bi-caret-down-fill {{ $arrowClass('bill', 'desc') }}"></i>
                                    </span>
                                </a>
                            </th>
                            <th>Documents</th>
                            @if ($canManageElectricity)
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($readings as $index => $reading)
                            @php
                                $service = $reading->electricityService;
                                $building = $service?->building;
                                $prevImported = $reading->getAttribute('computed_previous_imported');
                                $prevProduced = $reading->getAttribute('computed_previous_produced');
                                $consumption = $reading->getAttribute('computed_consumption');
                                $readingDate = $reading->reading_date ?? $reading->created_at;
                                $meterUrl = $reading->meter_image
                                    ? route('electricity-services.readings.files.show', [$service, $reading, 'meter'])
                                    : null;
                                $billUrl = $reading->bill_image
                                    ? route('electricity-services.readings.files.show', [$service, $reading, 'bill'])
                                    : null;
                                $importedCurrent = $reading->imported_current;
                                $producedCurrent = $reading->produced_current;
                                $savedEnergy = $reading->saved_energy;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-warning">{{ $service?->registration_number ?? 'N/A' }}
                                    </div>
                                    <small class="text-muted">{{ $building?->name ?? 'Unknown building' }}</small>
                                    @if ($service?->has_solar_power)
                                        <span class="badge bg-warning text-dark border ms-1 px-2 py-1"
                                            style="font-size: 0.7rem;">
                                            <i class="bi bi-sun-fill me-1"></i>Solar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ optional($readingDate)->format('F d, Y') ?? '—' }}</div>
                                    <small class="text-muted">Logged {{ $reading->created_at?->diffForHumans() }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="fw-semibold">{{ number_format((float) $consumption, 2) }}</div>
                                    <small class="text-muted d-block">Prev imported:
                                        {{ number_format((float) $prevImported, 2) }}</small>
                                    @if ($service?->has_solar_power)
                                        <small class="text-muted d-block">Prev produced:
                                            {{ number_format((float) $prevProduced, 2) }}</small>
                                    @endif
                                    <small class="text-muted d-block">Calculated imported:
                                        {{ number_format((float) ($reading->imported_calculated ?? 0), 2) }}</small>
                                    @if ($service?->has_solar_power && $reading->produced_calculated)
                                        <small class="text-muted d-block">Calculated produced:
                                            {{ number_format((float) $reading->produced_calculated, 2) }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (!is_null($reading->bill_amount))
                                        <div class="fw-semibold mb-1">
                                            {{ number_format((float) $reading->bill_amount, 2) }}</div>
                                    @else
                                        <div class="text-muted mb-1">—</div>
                                    @endif
                                    <span
                                        class="badge rounded-pill fw-semibold px-3 py-1 {{ $reading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}"
                                        style="font-size: 0.75rem;">
                                        {{ $reading->is_paid ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if ($meterUrl)
                                            <a href="{{ $meterUrl }}" class="btn btn-sm btn-outline-primary"
                                                target="_blank" title="View Meter Image">
                                                <i class="bi bi-camera"></i>
                                            </a>
                                        @endif
                                        @if ($billUrl)
                                            <a href="{{ $billUrl }}" class="btn btn-sm btn-outline-secondary"
                                                target="_blank" title="View Bill">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        @endif
                                        @if (!$meterUrl && !$billUrl)
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                @if ($canManageElectricity)
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('electricity-services.show', $service) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-reading-id="{{ $reading->id }}"
                                                data-reading-imported="{{ $reading->imported_calculated }}"
                                                data-reading-produced="{{ $reading->produced_calculated }}"
                                                data-reading-imported-current="{{ $importedCurrent }}"
                                                data-reading-produced-current="{{ $producedCurrent }}"
                                                data-reading-saved="{{ $savedEnergy }}"
                                                data-reading-bill="{{ $reading->bill_amount }}"
                                                data-reading-paid="{{ $reading->is_paid ? 1 : 0 }}"
                                                data-reading-date="{{ optional($readingDate)->format('Y-m-d') }}"
                                                data-reading-notes="{{ e($reading->notes ?? '') }}"
                                                data-service-reg="{{ $service?->registration_number ?? 'N/A' }}"
                                                data-service-building="{{ $building?->name ?? 'Unknown' }}"
                                                data-update-url="{{ route('electricity-services.readings.update', [$service, $reading]) }}"
                                                data-redirect-url="{{ request()->fullUrl() }}"
                                                data-meter-url="{{ $meterUrl }}" data-bill-url="{{ $billUrl }}"
                                                data-has-solar="{{ $service?->has_solar_power ? 1 : 0 }}"
                                                data-prev-imported="{{ $prevImported }}"
                                                data-prev-produced="{{ $prevProduced }}"
                                                onclick="openElectricReadingModal(this)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="openElectricDeleteModal({{ $reading->id }}, '{{ $service?->registration_number }}', '{{ route('electricity-services.readings.destroy', [$service, $reading]) }}')"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageElectricity ? 7 : 6 }}" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3 mb-1 fw-semibold">No readings available</p>
                                    <small>لا توجد قراءات متاحة حالياً</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($readings->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $readings->links() }}
            </div>
        @endif
    </div>

    @if ($canManageElectricity)
        <div class="modal fade" id="electricReadingModal" tabindex="-1" aria-labelledby="electricReadingModalLabel"
            aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="electricReadingModalLabel">Edit Monthly Reading</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="electricReadingForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                        <div class="modal-body">
                            <div class="alert alert-info d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle text-info fs-4"></i>
                                <div>
                                    <div><strong>Service:</strong> <span id="electricModalServiceReg">--</span></div>
                                    <div class="text-muted small">Building: <span id="electricModalServiceBuilding">--</span></div>
                                    <div class="text-muted small mt-1" id="electricModalPrevious">Previous imported calculated: 0.00</div>
                                    <div class="text-muted small" id="electricModalPreviousProduced" hidden>Previous produced calculated: 0.00</div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="electric_reading_date" class="form-label fw-bold">
                                        Reading Date <span class="text-muted">(تاريخ القراءة)</span> <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="electric_reading_date" name="reading_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="electric_imported_current" class="form-label fw-bold">
                                        Imported Current <span class="text-muted">(المستجره الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_imported_current" name="imported_current">
                                </div>
                                <div class="col-md-4">
                                    <label for="electric_imported" class="form-label fw-bold">
                                        Imported Calculated <span class="text-muted">(المستجره المحتسبه)</span> (kWh) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_imported" name="imported_calculated" required>
                                </div>
                                <div class="col-md-4 solar-only" id="electricProducedCurrentWrapper" hidden>
                                    <label for="electric_produced_current" class="form-label fw-bold">
                                        Produced Current <span class="text-muted">(المصدره الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_produced_current" name="produced_current">
                                </div>
                                <div class="col-md-4 solar-only" id="electricProducedWrapper" hidden>
                                    <label for="electric_produced" class="form-label fw-bold">
                                        Produced Calculated <span class="text-muted">(المصدره المحتسبه)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_produced" name="produced_calculated">
                                </div>
                                <div class="col-md-4 solar-only" id="electricSavedEnergyWrapper" hidden>
                                    <label for="electric_saved_energy" class="form-label fw-bold">
                                        Saved Energy <span class="text-muted">(الطاقة الموفرة)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_saved_energy" name="saved_energy">
                                </div>
                                <div class="col-md-3">
                                    <label for="electric_bill_amount" class="form-label fw-bold">
                                        Bill Amount <span class="text-muted">(قيمة الفاتورة)</span> (JOD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="electric_bill_amount" name="bill_amount">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold d-block">
                                        Payment Status <span class="text-muted">(حالة الدفع)</span>
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="electric_is_paid" name="is_paid" value="1">
                                        <label class="form-check-label" for="electric_is_paid">Mark as paid</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold d-block">
                                        Consumption Preview <span class="text-muted">(معاينة الاستهلاك)</span>
                                    </label>
                                    <div class="p-2 rounded border bg-light" id="electricConsumptionPreview">
                                        <i class="bi bi-calculator me-2 text-muted"></i>
                                        <span class="text-muted small">Adjust readings to preview consumption.</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="electric_notes" class="form-label fw-bold">
                                        Notes <span class="text-muted">(ملاحظات)</span>
                                    </label>
                                    <textarea class="form-control" id="electric_notes" name="notes" rows="2" placeholder="Optional remarks..."></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="electric_meter_image" class="form-label fw-bold">
                                        Meter Image <span class="text-muted">(صورة العداد)</span>
                                    </label>
                                    <input type="file" class="form-control" id="electric_meter_image" name="meter_image" accept=".jpg,.jpeg,.png">
                                    <small class="text-muted">JPG, PNG (max 4 MB)</small>
                                    <div id="electricMeterPreview" class="mt-2 d-none">
                                        <a id="electricMeterLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> View
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="electric_bill_image" class="form-label fw-bold">
                                        Bill Document <span class="text-muted">(مستند الفاتورة)</span>
                                    </label>
                                    <input type="file" class="form-control" id="electric_bill_image" name="bill_image" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (max 5 MB)</small>
                                    <div id="electricBillPreview" class="mt-2 d-none">
                                        <a id="electricBillLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark-text"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-save me-1"></i> Update Reading
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="electricDeleteModal" tabindex="-1" aria-labelledby="electricDeleteModalLabel"
            aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="electricDeleteModalLabel">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Are you sure you want to delete this reading?</p>
                        <div class="alert alert-warning mb-0">
                            <strong>Service:</strong> <span id="electricDeleteServiceReg"></span>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            <small>This action cannot be undone.</small>
                        </p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="electricDeleteForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .btn-orange {
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
        }

        .btn-orange:hover {
            background-color: #e56b00 !important;
            border-color: #e56b00 !important;
            color: white !important;
        }

        .btn-outline-orange {
            background-color: transparent !important;
            border-color: #ff7900 !important;
            color: #ff7900 !important;
        }

        .btn-outline-orange:hover {
            background-color: #ff7900 !important;
            border-color: #ff7900 !important;
            color: white !important;
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
@endpush

@push('scripts')
    @if ($canManageElectricity)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.openElectricReadingModal = function(button) {
                    const modalElement = document.getElementById('electricReadingModal');
                    if (!modalElement) {
                        console.error('Electric reading modal not found');
                        return;
                    }

                    const form = document.getElementById('electricReadingForm');
                    const methodInput = form.querySelector('input[name="_method"]');
                    const redirectInput = form.querySelector('input[name="redirect_to"]');

                    form.reset();
                    methodInput.value = 'PUT';
                    form.action = button.dataset.updateUrl;
                    redirectInput.value = button.dataset.redirectUrl || '{{ request()->fullUrl() }}';

                    // Populate all fields
                    document.getElementById('electric_reading_date').value = button.dataset.readingDate || '';
                    document.getElementById('electric_imported_current').value = button.dataset.readingImportedCurrent || '';
                    document.getElementById('electric_imported').value = button.dataset.readingImported || '';
                    document.getElementById('electric_produced_current').value = button.dataset.readingProducedCurrent || '';
                    document.getElementById('electric_produced').value = button.dataset.readingProduced || '';
                    document.getElementById('electric_saved_energy').value = button.dataset.readingSaved || '';
                    document.getElementById('electric_bill_amount').value = button.dataset.readingBill || '';
                    document.getElementById('electric_notes').value = button.dataset.readingNotes || '';
                    document.getElementById('electric_is_paid').checked = button.dataset.readingPaid === '1';

                    document.getElementById('electricModalServiceReg').textContent = button.dataset.serviceReg || '--';
                    document.getElementById('electricModalServiceBuilding').textContent = button.dataset.serviceBuilding || '--';

                    const prevImported = parseFloat(button.dataset.prevImported || '0').toFixed(2);
                    const prevProduced = parseFloat(button.dataset.prevProduced || '0').toFixed(2);
                    const hasSolar = button.dataset.hasSolar === '1';

                    const prevImportedLabel = document.getElementById('electricModalPrevious');
                    const prevProducedLabel = document.getElementById('electricModalPreviousProduced');

                    // Show/hide solar fields
                    const producedCurrentWrapper = document.getElementById('electricProducedCurrentWrapper');
                    const producedWrapper = document.getElementById('electricProducedWrapper');
                    const savedEnergyWrapper = document.getElementById('electricSavedEnergyWrapper');

                    prevImportedLabel.textContent = 'Previous imported calculated: ' + prevImported + ' kWh';

                    if (hasSolar) {
                        prevProducedLabel.textContent = 'Previous produced calculated: ' + prevProduced + ' kWh';
                        prevProducedLabel.hidden = false;
                        producedCurrentWrapper.hidden = false;
                        producedWrapper.hidden = false;
                        savedEnergyWrapper.hidden = false;
                    } else {
                        prevProducedLabel.hidden = true;
                        producedCurrentWrapper.hidden = true;
                        producedWrapper.hidden = true;
                        savedEnergyWrapper.hidden = true;
                        document.getElementById('electric_produced_current').value = '';
                        document.getElementById('electric_produced').value = '';
                        document.getElementById('electric_saved_energy').value = '';
                    }

                    // Update consumption preview
                    updateElectricConsumptionPreview(prevImported, prevProduced, hasSolar);

                    const meterPreview = document.getElementById('electricMeterPreview');
                    const meterLink = document.getElementById('electricMeterLink');
                    const billPreview = document.getElementById('electricBillPreview');
                    const billLink = document.getElementById('electricBillLink');

                    if (button.dataset.meterUrl) {
                        meterLink.href = button.dataset.meterUrl;
                        meterPreview.classList.remove('d-none');
                    } else {
                        meterPreview.classList.add('d-none');
                        meterLink.removeAttribute('href');
                    }

                    if (button.dataset.billUrl) {
                        billLink.href = button.dataset.billUrl;
                        billPreview.classList.remove('d-none');
                    } else {
                        billPreview.classList.add('d-none');
                        billLink.removeAttribute('href');
                    }

                    try {
                        const modal = new boosted.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true,
                            focus: true
                        });
                        modal.show();
                    } catch (error) {
                        console.error('Error showing electricity modal:', error);
                    }
                };

                function updateElectricConsumptionPreview(prevImported, prevProduced, hasSolar) {
                    const importedInput = document.getElementById('electric_imported');
                    const producedInput = document.getElementById('electric_produced');
                    const preview = document.getElementById('electricConsumptionPreview');

                    const imported = parseFloat(importedInput.value) || 0;
                    const produced = parseFloat(producedInput.value) || 0;

                    let message = '<i class="bi bi-calculator me-2 text-muted"></i><span class="text-muted small">Adjust readings to preview consumption.</span>';
                    let bgClass = 'bg-light';

                    if (hasSolar && imported > 0 && produced > 0) {
                        const importedDelta = imported - prevImported;
                        const producedDelta = produced - prevProduced;
                        const consumption = importedDelta - producedDelta;

                        if (consumption > 0) {
                            message = `<i class="bi bi-arrow-up-circle text-danger me-2"></i><strong>Consumption:</strong> ${consumption.toFixed(2)} kWh <span class="text-muted small">(Imported: ${importedDelta.toFixed(2)}, Produced: ${producedDelta.toFixed(2)})</span>`;
                            bgClass = 'bg-danger bg-opacity-10';
                        } else if (consumption < 0) {
                            message = `<i class="bi bi-arrow-down-circle text-success me-2"></i><strong>Net metering credit:</strong> ${Math.abs(consumption).toFixed(2)} kWh <span class="text-muted small">(excess production)</span>`;
                            bgClass = 'bg-success bg-opacity-10';
                        } else {
                            message = `<i class="bi bi-check-circle text-info me-2"></i><strong>Balanced:</strong> Production matches consumption`;
                            bgClass = 'bg-info bg-opacity-10';
                        }
                    } else if (!hasSolar && imported > 0) {
                        const consumption = imported - prevImported;
                        if (consumption > 0) {
                            message = `<i class="bi bi-lightning-charge text-warning me-2"></i><strong>Consumption:</strong> ${consumption.toFixed(2)} kWh`;
                            bgClass = 'bg-warning bg-opacity-10';
                        }
                    }

                    preview.className = `p-2 rounded border ${bgClass}`;
                    preview.innerHTML = message;
                }

                // Add event listeners for consumption preview
                ['electric_imported', 'electric_produced'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('input', function() {
                            const prevImported = parseFloat(document.getElementById('electricModalPrevious').textContent.match(/[\d.]+/)?.[0] || '0');
                            const prevProduced = parseFloat(document.getElementById('electricModalPreviousProduced').textContent.match(/[\d.]+/)?.[0] || '0');
                            const hasSolar = !document.getElementById('electricProducedWrapper').hidden;
                            updateElectricConsumptionPreview(prevImported, prevProduced, hasSolar);
                        });
                    }
                });

                window.openElectricDeleteModal = function(id, serviceReg, deleteUrl) {
                    const modalElement = document.getElementById('electricDeleteModal');
                    if (!modalElement) {
                        console.error('Electric delete modal not found');
                        return;
                    }

                    document.getElementById('electricDeleteServiceReg').textContent = serviceReg;
                    document.getElementById('electricDeleteForm').action = deleteUrl;

                    try {
                        const modal = new boosted.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true,
                            focus: true
                        });
                        modal.show();
                    } catch (error) {
                        console.error('Error showing delete modal:', error);
                    }
                };
            });
        </script>
    @endif
@endpush
