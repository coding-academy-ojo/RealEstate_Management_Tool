@extends('layouts.app')

@section('title', 'Water Bills')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water.services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item active">Water Bills</li>
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
            background-image: url("{{ asset('assets/images/water-drops.png') }}") !important;
            background-repeat: repeat !important;
            background-size: 20px 20px !important;
            opacity: 0.2;
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
        $currentUser = auth()->user();
        $canManageWater = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('water');
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
                'icon' => 'bi-building',
                'color' => 'text-info',
                'meta' => 'Total readings: ' . number_format($summary['total_readings'] ?? 0),
            ],
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-receipt-cutoff text-primary me-2"></i>
                Water Bills
                <small class="text-muted fs-6 fw-normal" style="font-family: 'Segoe UI', Tahoma, sans-serif;">لوحة
                    الفواتير</small>
            </h2>
            <p class="text-muted mb-0">
                Centralized view for every water reading, status, and billing attachment
                <span class="text-muted small d-block mt-1">عرض مركزي لجميع القراءات والفواتير</span>
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('water.services.index') }}" class="btn btn-orange">
                <i class="bi bi-list-ul me-1"></i> All Services
            </a>
            <a href="{{ route('water.overview') }}" class="btn btn-outline-orange">
                <i class="bi bi-speedometer2 me-1"></i> Overview
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        @foreach ($cards as $card)
            <div class="col">
                <div class="card card-hover metric-card border-0 shadow-lg h-100"
                    style="background-color: #1c1c1c !important;">
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
                <div class="col-12 col-md-4">
                    <label for="search" class="form-label fw-semibold">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                        placeholder="Search by service, building, company, or notes" value="{{ $filters['search'] }}">
                </div>
                <div class="col-12 col-md-3 col-lg-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5 col-lg-6 text-md-end">
                    <div class="d-flex flex-wrap justify-content-md-end gap-2">
                        <a href="{{ route('water.bills.index') }}" class="btn btn-light">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', 'date') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
            </form>
        </div>
    </div>

    @php
        $activeSort = request('sort', 'date');
        $activeDirection = request('direction', 'desc');
        $filterParams = collect($filters ?? [])
            ->filter(fn($value) => $value !== null && $value !== '')
            ->reject(fn($value, $key) => $key === 'status' && $value === 'all')
            ->toArray();

        $buildSortUrl = function (string $column) use ($filterParams, $activeSort, $activeDirection) {
            $params = $filterParams;
            $params['sort'] = $column;
            $params['direction'] = $activeSort === $column && $activeDirection === 'asc' ? 'desc' : 'asc';

            return route('water.bills.index', $params);
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
                        <i class="bi bi-clipboard-data me-2 text-orange"></i>
                        All Water Readings
                    </h5>
                    <small class="text-muted">
                        Showing {{ number_format($filteredCount) }} entries ({{ number_format($filteredUnpaidCount) }}
                        unpaid, {{ number_format($filteredPaidCount) }} paid)
                    </small>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    Total Consumption: {{ number_format($summary['total_consumption'] ?? 0, 2) }} m³
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
                                    <span>Consumption (m³)</span>
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
                            @if ($canManageWater)
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($readings as $index => $reading)
                            @php
                                $service = $reading->waterService;
                                $building = $service?->building;
                                $previousReading = $reading->getAttribute('computed_previous_reading');
                                $consumption =
                                    $reading->consumption_value ?? $reading->getAttribute('computed_consumption');
                                $readingDate = $reading->reading_date ?? $reading->created_at;
                                $meterUrl = $reading->meter_image
                                    ? route('water-services.readings.files.show', [$service, $reading, 'meter'])
                                    : null;
                                $billUrl = $reading->bill_image
                                    ? route('water-services.readings.files.show', [$service, $reading, 'bill'])
                                    : null;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-primary">{{ $service?->registration_number ?? 'N/A' }}
                                    </div>
                                    <small class="text-muted">{{ $building?->name ?? 'Unknown building' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ optional($readingDate)->format('F d, Y') ?? '—' }}</div>
                                    <small class="text-muted">Logged {{ $reading->created_at?->diffForHumans() }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="fw-semibold">{{ number_format((float) $consumption, 2) }}</div>
                                    <small class="text-muted d-block">Prev:
                                        {{ number_format((float) $previousReading, 2) }}</small>
                                    <small class="text-muted d-block">Curr:
                                        {{ number_format((float) ($reading->current_reading ?? 0), 2) }}</small>
                                </td>
                                <td class="text-end">
                                    @if (!is_null($reading->bill_amount))
                                        <div class="fw-semibold mb-1">
                                            {{ number_format((float) $reading->bill_amount, 2) }}</div>
                                    @else
                                        <div class="text-muted mb-1">—</div>
                                    @endif
                                    <span
                                        class="badge rounded-pill fw-semibold {{ $reading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
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
                                @if ($canManageWater)
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('water-services.show', $service) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-reading-id="{{ $reading->id }}"
                                                data-reading-current="{{ $reading->current_reading }}"
                                                data-reading-bill="{{ $reading->bill_amount }}"
                                                data-reading-paid="{{ $reading->is_paid ? 1 : 0 }}"
                                                data-reading-date="{{ optional($readingDate)->format('Y-m-d') }}"
                                                data-reading-notes="{{ e($reading->notes ?? '') }}"
                                                data-reading-previous="{{ $previousReading }}"
                                                data-reading-meter-url="{{ $meterUrl }}"
                                                data-reading-bill-url="{{ $billUrl }}"
                                                data-service-reg="{{ $service?->registration_number ?? 'N/A' }}"
                                                data-service-building="{{ $building?->name ?? 'Unknown' }}"
                                                data-update-url="{{ route('water-services.readings.update', [$service, $reading]) }}"
                                                data-redirect-url="{{ request()->fullUrl() }}"
                                                onclick="openReadingModalFromButton(this)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="openDeleteReadingModal({{ $reading->id }}, '{{ $service?->registration_number }}', '{{ route('water-services.readings.destroy', [$service, $reading]) }}')"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManageWater ? 6 : 5 }}" class="text-center text-muted py-5">
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

    @if ($canManageWater)
        <!-- Edit Reading Modal -->
        <div class="modal fade" id="globalReadingModal" tabindex="-1" aria-labelledby="globalReadingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalReadingModalLabel">
                            Edit Monthly Reading
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="globalReadingForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">
                        <div class="modal-body">
                            <div class="alert alert-info d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle text-info fs-4"></i>
                                <div>
                                    <div><strong>Service:</strong> <span id="modalServiceReg">--</span></div>
                                    <div class="text-muted small">Building: <span id="modalServiceBuilding">--</span>
                                    </div>
                                    <div class="text-muted small mt-1" id="modalPreviousHint">Previous reading: 0.00 m³
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="reading_date" class="form-label fw-bold">
                                        Reading Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="reading_date" name="reading_date"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="current_reading" class="form-label fw-bold">
                                        Current Reading (m³) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="current_reading"
                                        name="current_reading" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="bill_amount" class="form-label fw-bold">
                                        Bill Amount (JOD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="bill_amount" name="bill_amount">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold d-block">Payment Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid"
                                            value="1">
                                        <label class="form-check-label" for="is_paid">Mark as paid</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label fw-bold">
                                        Notes
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Optional remarks about this reading..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="meter_image" class="form-label fw-bold">
                                        Meter Image
                                    </label>
                                    <input type="file" class="form-control" id="meter_image" name="meter_image"
                                        accept=".jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted: JPG, PNG (max 4 MB)</small>
                                    <div id="meterPreview" class="mt-2 d-none">
                                        <a id="meterLink" href="#" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> View current meter image
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="bill_image" class="form-label fw-bold">
                                        Bill Document
                                    </label>
                                    <input type="file" class="form-control" id="bill_image" name="bill_image"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted: PDF, JPG, PNG (max 5 MB)</small>
                                    <div id="billPreview" class="mt-2 d-none">
                                        <a id="billLink" href="#" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark-text"></i> View current bill
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

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteReadingModal" tabindex="-1" aria-labelledby="deleteReadingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteReadingModalLabel">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Are you sure you want to delete this reading?</p>
                        <div class="alert alert-warning mb-0">
                            <strong>Service:</strong> <span id="deleteServiceReg"></span>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            <small>This action cannot be undone.</small>
                        </p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <form id="deleteReadingForm" method="POST" class="d-inline">
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

        /* Fix modal z-index issues */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1060 !important;
        }

        /* Ensure modals appear above everything */
        body.modal-open {
            overflow: hidden;
        }

        body.modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
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
    @if ($canManageWater)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.openReadingModalFromButton = function(button) {
                    const modalElement = document.getElementById('globalReadingModal');
                    if (!modalElement) {
                        console.error('Modal element not found');
                        return;
                    }

                    const form = document.getElementById('globalReadingForm');
                    const methodInput = form.querySelector('input[name="_method"]');
                    const redirectInput = form.querySelector('input[name="redirect_to"]');

                    // Reset form
                    form.reset();
                    methodInput.value = 'PUT';
                    form.action = button.dataset.updateUrl;
                    redirectInput.value = button.dataset.redirectUrl || '{{ request()->fullUrl() }}';

                    // Populate fields
                    document.getElementById('reading_date').value = button.dataset.readingDate || '';
                    document.getElementById('current_reading').value = button.dataset.readingCurrent || '';
                    document.getElementById('bill_amount').value = button.dataset.readingBill || '';
                    document.getElementById('notes').value = button.dataset.readingNotes || '';
                    document.getElementById('is_paid').checked = button.dataset.readingPaid === '1';

                    // Update service info
                    const previous = parseFloat(button.dataset.readingPrevious || '0').toFixed(2);
                    document.getElementById('modalPreviousHint').textContent =
                        'Previous reading before this entry: ' + previous + ' m³';
                    document.getElementById('modalServiceReg').textContent = button.dataset.serviceReg || '--';
                    document.getElementById('modalServiceBuilding').textContent = button.dataset.serviceBuilding ||
                        '--';

                    // Handle file previews
                    const meterPreview = document.getElementById('meterPreview');
                    const meterLink = document.getElementById('meterLink');
                    const billPreview = document.getElementById('billPreview');
                    const billLink = document.getElementById('billLink');

                    if (button.dataset.readingMeterUrl) {
                        meterLink.href = button.dataset.readingMeterUrl;
                        meterPreview.classList.remove('d-none');
                    } else {
                        meterPreview.classList.add('d-none');
                        meterLink.removeAttribute('href');
                    }

                    if (button.dataset.readingBillUrl) {
                        billLink.href = button.dataset.readingBillUrl;
                        billPreview.classList.remove('d-none');
                    } else {
                        billPreview.classList.add('d-none');
                        billLink.removeAttribute('href');
                    }

                    // Show modal
                    try {
                        const modal = new boosted.Modal(modalElement, {
                            backdrop: true,
                            keyboard: true,
                            focus: true
                        });
                        modal.show();
                    } catch (error) {
                        console.error('Error showing modal:', error);
                    }
                };

                window.openDeleteReadingModal = function(readingId, serviceReg, deleteUrl) {
                    const modalElement = document.getElementById('deleteReadingModal');
                    if (!modalElement) {
                        console.error('Delete modal element not found');
                        return;
                    }

                    document.getElementById('deleteServiceReg').textContent = serviceReg;
                    document.getElementById('deleteReadingForm').action = deleteUrl;

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
