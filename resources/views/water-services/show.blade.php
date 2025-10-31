@extends('layouts.app')

@section('title', 'Water Service Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water-services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item active">{{ $waterService->registration_number }}</li>
@endsection

@section('content')
    @php
        $currentUser = auth()->user();
        $canManageWater = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('water');
        $unpaidCount = $readings->where('is_paid', false)->count();
    @endphp

    @if (!$waterService->is_active)
        <div class="alert alert-danger border-danger d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fa-2x me-3 text-danger"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1"><i class="bi bi-x-circle me-2"></i>Service Deactivated</h5>
                <p class="mb-0">
                    <strong>Reason:</strong>
                    @switch($waterService->deactivation_reason)
                        @case('cancelled') Service Cancelled @break
                        @case('meter_changed') Meter Changed @break
                        @case('merged') Merged with Another Service @break
                        @case('other') Other Reason @break
                        @default Unknown @break
                    @endswitch
                    @if ($waterService->deactivation_date)
                        | <strong>Date:</strong> {{ $waterService->deactivation_date->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-droplet-fill text-info me-2"></i>
                Water Service Record
            </h2>
            <p class="text-muted mb-0">
                Registration #{{ $waterService->registration_number }}
                @if ($waterService->building)
                    · {{ $waterService->building->name }}
                @else
                    · Unassigned Building
                @endif
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($canManageWater)
                @if ($waterService->is_active)
                    <button type="button" class="btn btn-orange" onclick="openReadingModal('create')">
                        <i class="bi bi-plus-circle me-1"></i> Add Reading
                    </button>
                    <a href="{{ route('water-services.edit', $waterService) }}" class="btn btn-outline-orange">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-warning" onclick="openDeactivateModal()">
                        <i class="bi bi-pause-circle me-1"></i> Deactivate
                    </button>
                @else
                    <button type="button" class="btn btn-success" onclick="openReactivateModal()">
                        <i class="bi bi-play-circle me-1"></i> Reactivate
                    </button>
                @endif
                <button type="button" class="btn btn-outline-danger"
                    onclick="openDeleteModal('{{ $waterService->id }}', '{{ $waterService->registration_number }}', '{{ $waterService->company_name }}')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2 text-orange"></i>Building Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($waterService->building)
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <strong class="text-muted">Building:</strong>
                            </div>
                            <div class="col-md-8">
                                <a href="{{ route('buildings.show', $waterService->building) }}" class="text-decoration-none fw-semibold">
                                    {{ $waterService->building->code }} · {{ $waterService->building->name }}
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong class="text-muted">Site:</strong>
                            </div>
                            <div class="col-md-8">
                                @if ($waterService->building->site)
                                    <a href="{{ route('sites.show', $waterService->building->site) }}" class="text-decoration-none">
                                        {{ $waterService->building->site->code }} · {{ $waterService->building->site->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Site information not available.</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Building information not available.</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-orange"></i>Service Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Company Name:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $waterService->company_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Meter Owner:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $waterService->meter_owner_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Registration Number:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $waterService->registration_number }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Iron Number:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $waterService->iron_number ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Reference Image:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($waterService->initial_meter_image)
                                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                                    <img src="{{ route('water-services.files.show', [$waterService, 'reference-meter']) }}" alt="Reference image"
                                        class="rounded border" style="max-width: 160px; max-height: 160px; object-fit: cover;">
                                    <a href="{{ route('water-services.files.show', [$waterService, 'reference-meter']) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i> View full image
                                    </a>
                                </div>
                            @else
                                <span class="text-muted">No reference image uploaded.</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong class="text-muted">Remarks & Notes:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($waterService->remarks)
                                <div class="bg-light border rounded-3 p-3">
                                    {!! nl2br(e($waterService->remarks)) !!}
                                </div>
                            @else
                                <span class="text-muted">No additional remarks.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 me-2 text-orange"></i>Reading History
                    </h5>
                    @if ($canManageWater)
                        <button type="button" class="btn btn-sm btn-orange" onclick="openReadingModal('create')">
                            <i class="bi bi-plus-circle me-1"></i> Add Reading
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive reading-table-wrapper">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Reading Date</th>
                                    <th class="text-end">Previous (m³)</th>
                                    <th class="text-end">Current (m³)</th>
                                    <th class="text-end">Consumption (m³)</th>
                                    <th class="text-end">Bill (JOD)</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Documents</th>
                                    @if ($canManageWater)
                                        <th class="text-center">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($readings as $index => $reading)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $reading->reading_date?->format('F d, Y') ?? '—' }}</div>
                                            <small class="text-muted">Logged {{ $reading->created_at?->diffForHumans() }}</small>
                                        </td>
                                        @php
                                            $computedPrevious = (float) $reading->getAttribute('computed_previous_reading');
                                            $computedConsumption = $reading->getAttribute('computed_consumption');
                                        @endphp
                                        <td class="text-end">
                                            {{ number_format($computedPrevious, 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float) $reading->current_reading, 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format((float) $computedConsumption, 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ is_null($reading->bill_amount) ? '—' : number_format((float) $reading->bill_amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill fw-semibold {{ $reading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                                {{ $reading->is_paid ? 'Paid' : 'Unpaid' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($reading->notes)
                                                {{ \Illuminate\Support\Str::limit($reading->notes, 70) }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if ($reading->meter_image)
                                                    <a href="{{ route('water-services.readings.files.show', [$waterService, $reading, 'meter']) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-primary" title="View Meter Image">
                                                        <i class="bi bi-camera"></i>
                                                    </a>
                                                @endif
                                                @if ($reading->bill_image)
                                                    <a href="{{ route('water-services.readings.files.show', [$waterService, $reading, 'bill']) }}"
                                                        target="_blank" class="btn btn-sm btn-outline-secondary" title="View Bill">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                @endif
                                                @if (!$reading->meter_image && !$reading->bill_image)
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </div>
                                        </td>
                                        @if ($canManageWater)
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-reading-id="{{ $reading->id }}"
                                                        data-reading-current="{{ $reading->current_reading }}"
                                                        data-reading-bill="{{ $reading->bill_amount }}"
                                                        data-reading-paid="{{ $reading->is_paid ? 1 : 0 }}"
                                                        data-reading-date="{{ $reading->reading_date?->format('Y-m-d') }}"
                                                        data-reading-notes="{{ $reading->notes }}"
                                                        data-reading-previous="{{ $reading->getAttribute('computed_previous_reading') }}"
                                                        data-reading-meter-url="{{ $reading->meter_image ? route('water-services.readings.files.show', [$waterService, $reading, 'meter']) : '' }}"
                                                        data-reading-bill-url="{{ $reading->bill_image ? route('water-services.readings.files.show', [$waterService, $reading, 'bill']) : '' }}"
                                                        onclick="openReadingModalFromButton(this)">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('water-services.readings.destroy', [$waterService, $reading]) }}"
                                                        method="POST" onsubmit="return confirm('Delete this reading entry?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canManageWater ? 10 : 9 }}" class="text-center py-4">
                                            <i class="bi bi-droplet" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2 mb-0">No readings recorded yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <h6 class="text-white mb-3">
                        <i class="bi bi-cash-stack me-2"></i>Outstanding Balance
                    </h6>
                    <div class="display-5 fw-bold mb-1">
                        {{ number_format((float) $outstandingAmount, 2) }} <small class="fs-5">JOD</small>
                    </div>
                    <div class="text-white-50">
                        {{ $unpaidCount }} unpaid bill{{ $unpaidCount === 1 ? '' : 's' }}
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-orange"></i>Key Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-muted d-block small">Latest Reading</span>
                        @if ($latestReading)
                            <div class="fw-semibold">
                                {{ number_format((float) $latestReading->current_reading, 2) }} m³
                            </div>
                            <small class="text-muted">{{ $latestReading->reading_date?->format('F d, Y') }}</small>
                        @else
                            <span class="text-muted">No readings recorded.</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block small">Total Consumption</span>
                        <div class="fw-semibold">{{ number_format((float) $totalConsumption, 2) }} m³</div>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted d-block small">Total Readings Logged</span>
                        <div class="fw-semibold">{{ $readings->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2 text-orange"></i>Record Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Created:</span>
                        <span class="small">{{ $waterService->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Updated:</span>
                        <span class="small">{{ $waterService->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reading Modal -->
    @if ($canManageWater)
        <div class="modal fade" id="readingModal" tabindex="-1" aria-labelledby="readingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="readingModalLabel">
                            <span>Add Monthly Reading</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="readingForm" method="POST" action="{{ route('water-services.readings.store', $waterService) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" id="readingMethod" value="POST">
                        <div class="modal-body">
                            <div class="alert alert-info d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle text-info fs-4"></i>
                                <div id="readingPreviousHint">No previous readings recorded.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="reading_date" class="form-label fw-bold">
                                        Reading Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="reading_date" name="reading_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="reading_current" class="form-label fw-bold">
                                        Current Reading (m³) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" class="form-control" id="reading_current" name="current_reading" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="reading_bill" class="form-label fw-bold">
                                        Bill Amount (JOD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="reading_bill" name="bill_amount">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold d-block">Payment Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="reading_is_paid" name="is_paid" value="1">
                                        <label class="form-check-label" for="reading_is_paid">Mark as paid</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="reading_notes" class="form-label fw-bold">
                                        Notes
                                    </label>
                                    <textarea class="form-control" id="reading_notes" name="notes" rows="3" placeholder="Optional remarks about this reading..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="reading_meter_image" class="form-label fw-bold">
                                        Meter Image
                                    </label>
                                    <input type="file" class="form-control" id="reading_meter_image" name="meter_image" accept=".jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted: JPG, PNG (max 4 MB)</small>
                                    <div id="readingMeterPreview" class="mt-2 d-none">
                                        <a id="readingMeterLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> View current meter image
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="reading_bill_image" class="form-label fw-bold">
                                        Bill Document
                                    </label>
                                    <input type="file" class="form-control" id="reading_bill_image" name="bill_image" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Accepted: PDF, JPG, PNG (max 5 MB)</small>
                                    <div id="readingBillPreview" class="mt-2 d-none">
                                        <a id="readingBillLink" href="#" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark-text"></i> View current bill
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-orange" id="readingSubmit">
                                <i class="bi bi-check-circle me-1"></i> Save Reading
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10">
                    <h5 class="modal-title" id="deactivateModalLabel">
                        <i class="bi bi-pause-circle text-warning me-2"></i>Deactivate Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('water-services.deactivate', $waterService) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Deactivating this service will prevent adding new readings until reactivated.
                        </div>

                        <div class="mb-3">
                            <label for="deactivation_reason" class="form-label fw-bold">
                                Deactivation Reason <span class="text-danger">*</span>
                                <span class="text-muted">(سبب التعطيل)</span>
                            </label>
                            <select class="form-select" id="deactivation_reason" name="deactivation_reason" required>
                                <option value="">Select a reason...</option>
                                <option value="cancelled">Service Cancelled (الخدمة أُلغيت)</option>
                                <option value="meter_changed">Meter Changed (تم تغيير العداد)</option>
                                <option value="merged">Merged with Another Service (تم الدمج)</option>
                                <option value="other">Other Reason (سبب آخر)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deactivation_date" class="form-label fw-bold">
                                Deactivation Date <span class="text-danger">*</span>
                                <span class="text-muted">(تاريخ التعطيل)</span>
                            </label>
                            <input type="date" class="form-control" id="deactivation_date"
                                name="deactivation_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pause-circle me-1"></i> Deactivate Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reactivate Modal -->
    <div class="modal fade" id="reactivateModal" tabindex="-1" aria-labelledby="reactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success bg-opacity-10">
                    <h5 class="modal-title" id="reactivateModalLabel">
                        <i class="bi bi-play-circle text-success me-2"></i>Reactivate Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('water-services.reactivate', $waterService) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Confirm:</strong> Are you sure you want to reactivate this service?
                        </div>

                        <p class="text-muted">
                            This will restore full functionality to the service, allowing you to add new readings.
                        </p>

                        @if ($waterService->deactivation_reason)
                            <div class="alert alert-info">
                                <strong>Previous Deactivation:</strong><br>
                                <strong>Reason:</strong>
                                @switch($waterService->deactivation_reason)
                                    @case('cancelled') Service Cancelled @break
                                    @case('meter_changed') Meter Changed @break
                                    @case('merged') Merged with Another Service @break
                                    @case('other') Other Reason @break
                                @endswitch
                                @if ($waterService->deactivation_date)
                                    <br><strong>Date:</strong> {{ $waterService->deactivation_date->format('d M Y') }}
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-play-circle me-1"></i> Reactivate Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                        <small>This action will move the service to trash. You can restore it later from the Deleted Services page.</small>
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
        const createReadingUrl = @json(route('water-services.readings.store', $waterService));
        const updateReadingUrlTemplate = @json(route('water-services.readings.update', [$waterService, '__reading__']));
    const latestReadingValue = @json($latestReading?->current_reading);

        function openDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('deleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('deleteServiceCompany').textContent = companyName;
            document.getElementById('deleteForm').action = '/water-services/' + serviceId;

            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openReadingModal(mode = 'create', data = null) {
            const modalElement = document.getElementById('readingModal');
            const form = document.getElementById('readingForm');
            const methodInput = document.getElementById('readingMethod');
            const previousHint = document.getElementById('readingPreviousHint');
            const submitButton = document.getElementById('readingSubmit');
            const meterPreview = document.getElementById('readingMeterPreview');
            const billPreview = document.getElementById('readingBillPreview');
            const meterLink = document.getElementById('readingMeterLink');
            const billLink = document.getElementById('readingBillLink');

            if (!modalElement) {
                return;
            }

            form.reset();
            methodInput.value = 'POST';
            form.action = createReadingUrl;
            submitButton.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Reading';
            meterPreview.classList.add('d-none');
            billPreview.classList.add('d-none');
            meterLink.removeAttribute('href');
            billLink.removeAttribute('href');

            if (mode === 'edit' && data) {
                modalElement.querySelector('.modal-title span').textContent = 'Edit Monthly Reading';
                methodInput.value = 'PUT';
                form.action = updateReadingUrlTemplate.replace('__reading__', data.id);
                document.getElementById('reading_current').value = data.current_reading ?? '';
                document.getElementById('reading_bill').value = data.bill_amount ?? '';
                document.getElementById('reading_date').value = data.reading_date ?? '';
                document.getElementById('reading_notes').value = data.notes ?? '';
                document.getElementById('reading_is_paid').checked = Boolean(data.is_paid);
                submitButton.innerHTML = '<i class="bi bi-save me-1"></i> Update Reading';

                if (data.previous_reading !== '' && data.previous_reading !== null && data.previous_reading !== undefined) {
                    previousHint.textContent = `Previous reading before this entry: ${parseFloat(data.previous_reading).toFixed(2)} m³`;
                } else {
                    previousHint.textContent = 'This is the first reading entry.';
                }

                if (data.meter_url) {
                    meterLink.href = data.meter_url;
                    meterPreview.classList.remove('d-none');
                }

                if (data.bill_url) {
                    billLink.href = data.bill_url;
                    billPreview.classList.remove('d-none');
                }
            } else {
                modalElement.querySelector('.modal-title span').textContent = 'Add Monthly Reading';
                document.getElementById('reading_is_paid').checked = false;
                if (latestReadingValue !== null && latestReadingValue !== undefined) {
                    previousHint.textContent = `Latest recorded reading: ${parseFloat(latestReadingValue).toFixed(2)} m³`;
                } else {
                    previousHint.textContent = 'No previous readings recorded.';
                }
            }

            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openReadingModalFromButton(button) {
            const dataset = button.dataset;
            openReadingModal('edit', {
                id: dataset.readingId,
                current_reading: dataset.readingCurrent || '',
                bill_amount: dataset.readingBill || '',
                is_paid: dataset.readingPaid === '1',
                reading_date: dataset.readingDate || '',
                notes: dataset.readingNotes || '',
                previous_reading: dataset.readingPrevious,
                meter_url: dataset.readingMeterUrl || null,
                bill_url: dataset.readingBillUrl || null,
            });
        }

        function openDeactivateModal() {
            const modalElement = document.getElementById('deactivateModal');
            if (modalElement) {
                const modal = new boosted.Modal(modalElement);
                modal.show();
            }
        }

        function openReactivateModal() {
            const modalElement = document.getElementById('reactivateModal');
            if (modalElement) {
                const modal = new boosted.Modal(modalElement);
                modal.show();
            }
        }
    </script>

    <style>
        .reading-table-wrapper {
            max-height: 420px;
            overflow-y: auto;
        }

        .reading-table-wrapper thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
        }
    </style>
@endsection
