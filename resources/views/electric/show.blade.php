@extends('layouts.app')

@section('title', 'Electricity Service Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">{{ $electricityService->registration_number }}</li>
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
    </style>

    @php
        $currentUser = auth()->user();
        $canManageElectricity = $currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('electricity');
        $isSolar = $electricityService->has_solar_power;
    @endphp

    @if ($isSolar)
        <div class="alert alert-success border-success d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-sun fa-2x me-3 text-success"></i>
            <div>
                <h5 class="alert-heading mb-1"><i class="fas fa-solar-panel me-2"></i>Solar / Net Metering Service</h5>
                <p class="mb-0">This service is connected to solar power generation system with net metering capability.
                </p>
            </div>
        </div>
    @endif

    @if (!$electricityService->is_active)
        <div class="alert alert-danger border-danger d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill fa-2x me-3 text-danger"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1"><i class="bi bi-x-circle me-2"></i>Service Deactivated</h5>
                <p class="mb-0">
                    <strong>Reason:</strong>
                    @switch($electricityService->deactivation_reason)
                        @case('cancelled')
                            Service Cancelled
                        @break

                        @case('meter_changed')
                            Meter Changed
                        @break

                        @case('merged')
                            Merged with Another Service
                        @break

                        @case('other')
                            Other Reason
                        @break

                        @default
                            Unknown
                        @break
                    @endswitch
                    @if ($electricityService->deactivation_date)
                        | <strong>Date:</strong> {{ $electricityService->deactivation_date->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4">
        <div>
            <h2 class="mb-1">
                @if ($isSolar)
                    <i class="fas fa-solar-panel text-success me-2"></i>
                @else
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                @endif
                Electricity Service Record
            </h2>
            <p class="text-muted mb-1">
                Registration #{{ $electricityService->registration_number }} · Meter {{ $electricityService->meter_number }}
            </p>
            @if ($isSolar)
                <span class="badge bg-success text-white fs-6"><i class="fas fa-sun me-1"></i>Solar / Net Metering</span>
            @else
                <span class="badge bg-secondary text-white fs-6"><i class="bi bi-lightning me-1"></i>Standard Supply</span>
            @endif
        </div>
        @if ($canManageElectricity)
            <div class="d-flex flex-wrap gap-2">
                @if ($electricityService->is_active)
                    <button type="button" class="btn btn-orange" onclick="openReadingModal('create')">
                        <i class="bi bi-plus-circle me-1"></i> Add Reading
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="openDisconnectionModal('create')">
                        <i class="bi bi-plug me-1"></i> Log Disconnection
                    </button>
                    <a href="{{ route('electricity-services.edit', $electricityService) }}" class="btn btn-outline-orange">
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
                    onclick="openDeleteModal('{{ $electricityService->id }}', '{{ $electricityService->registration_number }}', '{{ $electricityService->company_name }}')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        @endif
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
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2 text-orange"></i>Service Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle text-orange fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Subscriber</div>
                                    <div class="text-muted">{{ $electricityService->subscriber_name }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-cpu text-orange fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Meter Number</div>
                                    <div class="text-muted">{{ $electricityService->meter_number }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-building text-orange fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Building</div>
                                    @if ($electricityService->building)
                                        <a href="{{ route('buildings.show', $electricityService->building) }}"
                                            class="text-decoration-none">
                                            {{ $electricityService->building->name }}
                                            ({{ $electricityService->building->code }})
                                        </a>
                                        <div class="text-muted small">Site:
                                            {{ $electricityService->building->site->name ?? '—' }}</div>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-building-gear text-orange fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Company</div>
                                    <div class="text-muted">{{ $electricityService->company_name }}</div>
                                    <div class="text-muted small">Reg #: {{ $electricityService->registration_number }}
                                    </div>
                                    <div class="text-muted small">Solar / Net Metering: {{ $isSolar ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="mt-4">
                        <div class="fw-semibold mb-1">Remarks</div>
                        <div class="text-muted">{{ $electricityService->remarks ?: 'No remarks recorded.' }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2 align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-plug me-2 text-orange"></i>Connection & Disconnection History
                    </h5>
                    @if ($canManageElectricity)
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="openDisconnectionModal('create')">
                            <i class="bi bi-plus-circle me-1"></i> Log Event
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if ($electricityService->disconnections->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-plug" style="font-size: 3rem;"></i>
                            <p class="mt-2 mb-0">No disconnection events recorded.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Disconnection Date<br><small class="text-muted">تاريخ القطع</small></th>
                                        <th>Reconnection Date<br><small class="text-muted">تاريخ إعادة الوصل</small></th>
                                        <th>Reason<br><small class="text-muted">السبب</small></th>
                                        @if ($canManageElectricity)
                                            <th class="text-end">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($electricityService->disconnections as $event)
                                        <tr>
                                            <td>{{ $event->disconnection_date?->format('d M Y') ?? '—' }}</td>
                                            <td>
                                                @if ($event->reconnection_date)
                                                    {{ $event->reconnection_date->format('d M Y') }}
                                                @else
                                                    <span class="badge bg-danger text-white">Still disconnected</span>
                                                @endif
                                            </td>
                                            <td>{{ $event->reason ?: '—' }}</td>
                                            @if ($canManageElectricity)
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary"
                                                            onclick="openDisconnectionModal('edit', this)"
                                                            data-disconnection-id="{{ $event->id }}"
                                                            data-disconnection-date="{{ $event->disconnection_date?->format('Y-m-d') }}"
                                                            data-reconnection-date="{{ $event->reconnection_date?->format('Y-m-d') }}"
                                                            data-reason="{{ $event->reason }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('electricity-services.disconnections.destroy', [$electricityService, $event]) }}"
                                                            onsubmit="return confirm('Delete this disconnection record?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-orange mb-3">
                        <i class="bi bi-speedometer2 me-2"></i>Quick Stats
                    </h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-bolt fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Last Imported Calculated<br><span
                                        class="text-muted">المستجره المحتسبه</span></div>
                                <div class="fw-bold">
                                    {{ $latestImportedCalculated ? number_format((float) $latestImportedCalculated, 2) . ' kWh' : 'No data' }}
                                </div>
                            </div>
                        </div>
                        @if ($isSolar)
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <i class="fas fa-sun fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Last Produced Calculated<br><span
                                            class="text-muted">المصدره المحتسبه</span></div>
                                    <div class="fw-bold">
                                        {{ $latestProducedCalculated ? number_format((float) $latestProducedCalculated, 2) . ' kWh' : 'No data' }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning text-dark rounded-3 d-flex align-items-center justify-content-center"
                                    style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <i class="fas fa-battery-three-quarters fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Saved Energy<br><span class="text-muted">الطاقة
                                            الموفرة</span></div>
                                    <div class="fw-bold">
                                        {{ $latestSavedEnergy ? number_format((float) $latestSavedEnergy, 2) . ' kWh' : 'No data' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2 text-orange"></i>Documents</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        @if ($electricityService->reset_file)
                            <a href="{{ route('electricity-services.files.show', [$electricityService, 'reset']) }}"
                                class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-paperclip me-1"></i> Reset File
                            </a>
                        @else
                            <span class="text-muted">No reference documents uploaded.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-orange"></i>Record Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Created:</span>
                        <span class="small">{{ $electricityService->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Last Updated:</span>
                        <span class="small">{{ $electricityService->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Readings Section - Full Width -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between flex-wrap gap-2 align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-journal-text me-2 text-orange"></i>Monthly Readings
            </h5>
            @if ($canManageElectricity)
                <button type="button" class="btn btn-sm btn-orange" onclick="openReadingModal('create')">
                    <i class="bi bi-plus-circle me-1"></i> Add Reading
                </button>
            @endif
        </div>
        <div class="card-body">
            @if ($readings->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-lightning-charge" style="font-size: 3rem;"></i>
                    <p class="mt-2 mb-0">No readings recorded yet.</p>
                </div>
            @else
                <div class="reading-table-wrapper table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date<br><small class="text-muted">التاريخ</small></th>
                                <th>Imported Readings<br><small class="text-muted">المستجره (kWh)</small></th>
                                <th>Produced Readings<br><small class="text-muted">المصدره (kWh)</small></th>
                                <th>Saved Energy<br><small class="text-muted">الطاقة الموفرة (kWh)</small></th>
                                <th>Consumption<br><small class="text-muted">الاستهلاك (kWh)</small></th>
                                <th>Bill<br><small class="text-muted">الفاتورة</small></th>
                                <th>Notes<br><small class="text-muted">ملاحظات</small></th>
                                @if ($canManageElectricity)
                                    <th class="text-end">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($readings as $reading)
                                @php
                                    $importedCurrent = $reading->imported_current;
                                    $importedCalculated = $reading->imported_calculated;
                                    $producedCurrent = $reading->produced_current;
                                    $producedCalculated = $reading->produced_calculated;
                                    $savedEnergy = $reading->saved_energy;
                                    $consumption = $reading->computed_consumption ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $reading->reading_date?->format('d M Y') ?? 'No date' }}</div>
                                        <small class="text-muted">Recorded
                                            {{ $reading->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $importedCalculated !== null ? number_format((float) $importedCalculated, 2) : '—' }}
                                            <span class="text-muted">kWh</span>
                                        </div>
                                        <small class="text-muted d-block">Current:
                                            {{ $importedCurrent !== null ? number_format((float) $importedCurrent, 2) . ' kWh' : '—' }}</small>
                                        <small class="text-muted d-block">Prev. calculated:
                                            {{ number_format((float) ($reading->computed_previous_imported_calculated ?? 0), 2) }}
                                            kWh</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            {{ $producedCalculated !== null ? number_format((float) $producedCalculated, 2) : '—' }}
                                            <span class="text-muted">kWh</span>
                                        </div>
                                        <small class="text-muted d-block">Current:
                                            {{ $producedCurrent !== null ? number_format((float) $producedCurrent, 2) . ' kWh' : '—' }}</small>
                                        <small class="text-muted d-block">Prev. calculated:
                                            {{ number_format((float) ($reading->computed_previous_produced_calculated ?? 0), 2) }}
                                            kWh</small>
                                    </td>
                                    <td>{{ $savedEnergy !== null ? number_format((float) $savedEnergy, 2) . ' kWh' : '—' }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ number_format((float) $consumption, 2) }} kWh</div>
                                    </td>
                                    <td>
                                        @if ($reading->bill_amount !== null)
                                            <div class="fw-semibold">
                                                {{ number_format((float) $reading->bill_amount, 2) }}
                                                <span class="text-muted">JOD</span>
                                            </div>
                                            @if ($reading->is_paid)
                                                <span class="badge bg-success text-white">Paid</span>
                                            @else
                                                <span class="badge bg-danger text-white">Unpaid</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No bills recorded</span>
                                        @endif
                                    </td>
                                    <td>{{ $reading->notes ?: '—' }}</td>
                                    @if ($canManageElectricity)
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary"
                                                    onclick="openReadingModalFromButton(this)"
                                                    data-reading-id="{{ $reading->id }}"
                                                    data-imported-current="{{ $importedCurrent }}"
                                                    data-imported-calculated="{{ $importedCalculated }}"
                                                    data-produced-current="{{ $producedCurrent }}"
                                                    data-produced-calculated="{{ $producedCalculated }}"
                                                    data-saved-energy="{{ $savedEnergy }}"
                                                    data-reading-bill="{{ $reading->bill_amount }}"
                                                    data-reading-paid="{{ $reading->is_paid ? '1' : '0' }}"
                                                    data-reading-date="{{ $reading->reading_date?->format('Y-m-d') }}"
                                                    data-reading-notes="{{ $reading->notes }}"
                                                    data-prev-imported-calculated="{{ $reading->computed_previous_imported_calculated ?? 0 }}"
                                                    data-prev-produced-calculated="{{ $reading->computed_previous_produced_calculated ?? 0 }}"
                                                    data-reading-meter-url="{{ $reading->meter_image ? route('electricity-services.readings.files.show', [$electricityService, $reading, 'meter']) : '' }}"
                                                    data-reading-bill-url="{{ $reading->bill_image ? route('electricity-services.readings.files.show', [$electricityService, $reading, 'bill']) : '' }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form method="POST"
                                                    action="{{ route('electricity-services.readings.destroy', [$electricityService, $reading]) }}"
                                                    onsubmit="return confirm('Delete this reading?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if ($canManageElectricity)
        <!-- Reading Modal -->
        <div class="modal fade" id="readingModal" tabindex="-1" aria-labelledby="readingModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="readingModalLabel"><span>Add Monthly Reading</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="readingForm" method="POST"
                        action="{{ route('electricity-services.readings.store', $electricityService) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" id="readingMethod" value="POST">
                        <div class="modal-body">
                            <div class="alert alert-info d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle text-info fs-4"></i>
                                <div id="readingPreviousHint">No previous readings.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="reading_date" class="form-label fw-bold">
                                        Reading Date <span class="text-muted">(تاريخ القراءة)</span>
                                    </label>
                                    <input type="date" class="form-control" id="reading_date" name="reading_date">
                                </div>
                                <div class="col-md-4">
                                    <label for="reading_imported_current" class="form-label fw-bold">
                                        Imported Current <span class="text-muted">(المستجره الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_imported_current" name="imported_current">
                                </div>
                                <div class="col-md-4">
                                    <label for="reading_imported_calculated" class="form-label fw-bold">
                                        Imported Calculated <span class="text-muted">(المستجره المحتسبه)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_imported_calculated" name="imported_calculated">
                                </div>
                                <div class="col-md-4 solar-only d-none">
                                    <label for="reading_produced_current" class="form-label fw-bold">
                                        Produced Current <span class="text-muted">(المصدره الحالية)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_produced_current" name="produced_current">
                                </div>
                                <div class="col-md-4 solar-only d-none">
                                    <label for="reading_produced_calculated" class="form-label fw-bold">
                                        Produced Calculated <span class="text-muted">(المصدره المحتسبه)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_produced_calculated" name="produced_calculated">
                                </div>
                                <div class="col-md-4 solar-only d-none">
                                    <label for="reading_saved_energy" class="form-label fw-semibold">
                                        Saved Energy <span class="text-muted">(الطاقة الموفرة)</span> (kWh)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_saved_energy" name="saved_energy">
                                </div>
                                <div class="col-md-3">
                                    <label for="reading_bill" class="form-label fw-bold">
                                        Bill Amount <span class="text-muted">(قيمة الفاتورة)</span> (JOD)
                                    </label>
                                    <input type="number" step="0.01" min="0" class="form-control"
                                        id="reading_bill" name="bill_amount">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold d-block">
                                        Payment Status <span class="text-muted">(حالة الدفع)</span>
                                    </label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="reading_is_paid"
                                            name="is_paid" value="1">
                                        <label class="form-check-label" for="reading_is_paid">Mark as paid</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold d-block">
                                        Consumption Preview <span class="text-muted">(معاينة الاستهلاك)</span>
                                    </label>
                                    <div class="p-2 rounded border bg-light" id="readingConsumptionPreview">
                                        <i class="bi bi-calculator me-2 text-muted"></i>
                                        <span class="text-muted small">Adjust readings to preview consumption.</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="reading_notes" class="form-label fw-bold">
                                        Notes <span class="text-muted">(ملاحظات)</span>
                                    </label>
                                    <textarea class="form-control" id="reading_notes" name="notes" rows="2" placeholder="Optional remarks..."></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="reading_meter_image" class="form-label fw-bold">
                                        Meter Image <span class="text-muted">(صورة العداد)</span>
                                    </label>
                                    <input type="file" class="form-control" id="reading_meter_image"
                                        name="meter_image" accept=".jpg,.jpeg,.png">
                                    <small class="text-muted">JPG, PNG (max 4 MB)</small>
                                    <div id="readingMeterPreview" class="mt-2 d-none">
                                        <a id="readingMeterLink" href="#" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera"></i> View
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="reading_bill_image" class="form-label fw-bold">
                                        Bill Document <span class="text-muted">(مستند الفاتورة)</span>
                                    </label>
                                    <input type="file" class="form-control" id="reading_bill_image" name="bill_image"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">PDF, JPG, PNG (max 5 MB)</small>
                                    <div id="readingBillPreview" class="mt-2 d-none">
                                        <a id="readingBillLink" href="#" target="_blank"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-file-earmark-text"></i> View
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

    <!-- Disconnection Modal -->
    <div class="modal fade" id="disconnectionModal" tabindex="-1" aria-labelledby="disconnectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disconnectionModalLabel"><span>Log Disconnection</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="disconnectionForm" method="POST"
                    action="{{ route('electricity-services.disconnections.store', $electricityService) }}">
                    @csrf
                    <input type="hidden" name="_method" id="disconnectionMethod" value="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="disconnection_date" class="form-label fw-bold">
                                    Disconnection Date <span class="text-muted">(تاريخ القطع)</span>
                                </label>
                                <input type="date" class="form-control" id="disconnection_date"
                                    name="disconnection_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="reconnection_date" class="form-label fw-bold">
                                    Reconnection Date <span class="text-muted">(تاريخ إعادة الوصل)</span>
                                </label>
                                <input type="date" class="form-control" id="reconnection_date"
                                    name="reconnection_date">
                                <small class="text-muted">Leave empty if still disconnected</small>
                            </div>
                            <div class="col-12">
                                <label for="disconnection_reason" class="form-label fw-bold">
                                    Reason <span class="text-muted">(السبب)</span>
                                </label>
                                <textarea class="form-control" id="disconnection_reason" name="reason" rows="3"
                                    placeholder="Optional reason for disconnection..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-orange" id="disconnectionSubmit">
                            <i class="bi bi-check-circle me-1"></i> Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning bg-opacity-10">
                    <h5 class="modal-title" id="deactivateModalLabel">
                        <i class="bi bi-pause-circle text-warning me-2"></i>Deactivate Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('electricity-services.deactivate', $electricityService) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Deactivating this service will prevent adding new readings until
                            reactivated.
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
                            <input type="date" class="form-control" id="deactivation_date" name="deactivation_date"
                                value="{{ date('Y-m-d') }}" required>
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
    <div class="modal fade" id="reactivateModal" tabindex="-1" aria-labelledby="reactivateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success bg-opacity-10">
                    <h5 class="modal-title" id="reactivateModalLabel">
                        <i class="bi bi-play-circle text-success me-2"></i>Reactivate Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('electricity-services.reactivate', $electricityService) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Confirm:</strong> Are you sure you want to reactivate this service?
                        </div>

                        <p class="text-muted">
                            This will restore full functionality to the service, allowing you to add new readings and manage
                            disconnections.
                        </p>

                        @if ($electricityService->deactivation_reason)
                            <div class="alert alert-info">
                                <strong>Previous Deactivation:</strong><br>
                                <strong>Reason:</strong>
                                @switch($electricityService->deactivation_reason)
                                    @case('cancelled')
                                        Service Cancelled
                                    @break

                                    @case('meter_changed')
                                        Meter Changed
                                    @break

                                    @case('merged')
                                        Merged with Another Service
                                    @break

                                    @case('other')
                                        Other Reason
                                    @break
                                @endswitch
                                @if ($electricityService->deactivation_date)
                                    <br><strong>Date:</strong>
                                    {{ $electricityService->deactivation_date->format('d M Y') }}
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
        const createReadingUrl = @json(route('electricity-services.readings.store', $electricityService));
        const updateReadingUrlTemplate = @json(route('electricity-services.readings.update', [$electricityService, '__reading__']));
        const serviceIsSolar = @json($isSolar);
        const latestImportedCurrent = parseFloat(@json($latestImportedCurrent ?? 0)) || 0;
        const latestImportedCalculated = parseFloat(@json($latestImportedCalculated ?? 0)) || 0;
        const latestProducedCurrent = parseFloat(@json($latestProducedCurrent ?? 0)) || 0;
        const latestProducedCalculated = parseFloat(@json($latestProducedCalculated ?? 0)) || 0;

        const createDisconnectionUrl = @json(route('electricity-services.disconnections.store', $electricityService));
        const updateDisconnectionUrlTemplate = @json(route('electricity-services.disconnections.update', [$electricityService, '__disconnection__']));

        function openDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('deleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('deleteServiceCompany').textContent = companyName;
            document.getElementById('deleteForm').action = '/electricity-services/' + serviceId;

            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function toggleSolarFields(isSolar) {
            document.querySelectorAll('.solar-only').forEach((element) => {
                element.classList.toggle('d-none', !isSolar);
            });
        }

        function setFieldRequirements(isSolar) {
            document.getElementById('reading_produced_current').required = isSolar;
            document.getElementById('reading_produced_calculated').required = isSolar;
            document.getElementById('reading_saved_energy').required = isSolar;
        }

        function openReadingModal(mode = 'create', data = null) {
            const modalElement = document.getElementById('readingModal');
            const form = document.getElementById('readingForm');
            const methodInput = document.getElementById('readingMethod');
            const submitButton = document.getElementById('readingSubmit');
            const previousHint = document.getElementById('readingPreviousHint');
            const consumptionPreview = document.getElementById('readingConsumptionPreview');
            const meterPreview = document.getElementById('readingMeterPreview');
            const billPreview = document.getElementById('readingBillPreview');
            const meterLink = document.getElementById('readingMeterLink');
            const billLink = document.getElementById('readingBillLink');

            const importedCurrentInput = document.getElementById('reading_imported_current');
            const importedCalculatedInput = document.getElementById('reading_imported_calculated');
            const producedCurrentInput = document.getElementById('reading_produced_current');
            const producedCalculatedInput = document.getElementById('reading_produced_calculated');
            const savedEnergyInput = document.getElementById('reading_saved_energy');

            if (!modalElement) {
                return;
            }

            form.reset();
            methodInput.value = 'POST';
            form.action = createReadingUrl;
            submitButton.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Reading';
            modalElement.querySelector('.modal-title span').textContent = 'Add Monthly Reading';
            meterPreview.classList.add('d-none');
            billPreview.classList.add('d-none');
            meterLink.removeAttribute('href');
            billLink.removeAttribute('href');
            document.getElementById('reading_is_paid').checked = false;

            toggleSolarFields(serviceIsSolar);
            setFieldRequirements(serviceIsSolar);

            let previousImportedCalculated = latestImportedCalculated;
            let previousProducedCalculated = latestProducedCalculated;

            if (mode === 'edit' && data) {
                modalElement.querySelector('.modal-title span').textContent = 'Edit Monthly Reading';
                methodInput.value = 'PUT';
                form.action = updateReadingUrlTemplate.replace('__reading__', data.id);

                document.getElementById('reading_date').value = data.reading_date ?? '';
                importedCurrentInput.value = data.imported_current ?? '';
                importedCalculatedInput.value = data.imported_calculated ?? '';
                producedCurrentInput.value = data.produced_current ?? '';
                producedCalculatedInput.value = data.produced_calculated ?? '';
                savedEnergyInput.value = data.saved_energy ?? '';
                document.getElementById('reading_bill').value = data.bill_amount ?? '';
                document.getElementById('reading_notes').value = data.notes ?? '';
                document.getElementById('reading_is_paid').checked = Boolean(data.is_paid);

                submitButton.innerHTML = '<i class="bi bi-save me-1"></i> Update Reading';

                previousImportedCalculated = parseFloat(data.prev_imported_calculated ?? previousImportedCalculated) || 0;
                previousProducedCalculated = parseFloat(data.prev_produced_calculated ?? previousProducedCalculated) || 0;

                if (data.meter_url) {
                    meterLink.href = data.meter_url;
                    meterPreview.classList.remove('d-none');
                }

                if (data.bill_url) {
                    billLink.href = data.bill_url;
                    billPreview.classList.remove('d-none');
                }
            } else {
                savedEnergyInput.value = '';
            }

            importedCalculatedInput.dataset.previous = previousImportedCalculated;
            producedCalculatedInput.dataset.previous = previousProducedCalculated;

            const previousText = serviceIsSolar ?
                `Previous imported: ${previousImportedCalculated.toFixed(2)} kWh | produced: ${previousProducedCalculated.toFixed(2)} kWh` :
                `Previous imported (calculated): ${previousImportedCalculated.toFixed(2)} kWh`;
            previousHint.textContent = previousText;

            updateConsumptionPreview();

            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openReadingModalFromButton(button) {
            const dataset = button.dataset;
            openReadingModal('edit', {
                id: dataset.readingId,
                imported_current: dataset.importedCurrent || '',
                imported_calculated: dataset.importedCalculated || '',
                produced_current: dataset.producedCurrent || '',
                produced_calculated: dataset.producedCalculated || '',
                saved_energy: dataset.savedEnergy || '',
                bill_amount: dataset.readingBill || '',
                is_paid: dataset.readingPaid === '1',
                reading_date: dataset.readingDate || '',
                notes: dataset.readingNotes || '',
                prev_imported_calculated: dataset.prevImportedCalculated || '',
                prev_produced_calculated: dataset.prevProducedCalculated || '',
                meter_url: dataset.readingMeterUrl || null,
                bill_url: dataset.readingBillUrl || null,
            });
        }

        function updateConsumptionPreview() {
            const importedCalculatedInput = document.getElementById('reading_imported_calculated');
            const producedCalculatedInput = document.getElementById('reading_produced_calculated');
            const consumptionPreview = document.getElementById('readingConsumptionPreview');

            const importedCalculated = parseFloat(importedCalculatedInput.value);
            const producedCalculated = parseFloat(producedCalculatedInput.value);

            const prevImportedCalculated = parseFloat(importedCalculatedInput.dataset.previous ?? '0');
            const prevProducedCalculated = parseFloat(producedCalculatedInput.dataset.previous ?? '0');

            let message =
                '<i class="bi bi-calculator me-2 text-muted"></i><span class="text-muted small">Adjust readings to preview consumption.</span>';
            let baseClasses = 'p-2 rounded border';
            let bgClass = 'bg-light';

            if (serviceIsSolar) {
                if (Number.isNaN(importedCalculated) || Number.isNaN(producedCalculated)) {
                    message =
                        '<i class="bi bi-info-circle me-2 text-info"></i><span class="text-muted small">Enter imported and produced calculated readings to preview consumption.</span>';
                } else {
                    const importedDelta = importedCalculated - prevImportedCalculated;
                    const producedDelta = producedCalculated - prevProducedCalculated;
                    const consumption = importedDelta - producedDelta;
                    if (consumption >= 0) {
                        bgClass = 'bg-success text-white';
                        message =
                            `<i class="bi bi-check-circle me-2"></i><strong class="small">Consumption: ${consumption.toFixed(2)} kWh</strong>`;
                    } else {
                        bgClass = 'bg-warning text-dark';
                        message =
                            `<i class="bi bi-exclamation-triangle me-2"></i><strong class="small">Consumption: ${consumption.toFixed(2)} kWh</strong>`;
                    }
                }
            } else {
                if (Number.isNaN(importedCalculated)) {
                    message =
                        '<i class="bi bi-info-circle me-2 text-info"></i><span class="text-muted small">Enter imported calculated reading to preview consumption.</span>';
                } else {
                    const consumption = importedCalculated - prevImportedCalculated;
                    if (consumption >= 0) {
                        bgClass = 'bg-success text-white';
                        message =
                            `<i class="bi bi-check-circle me-2"></i><strong class="small">Consumption: ${consumption.toFixed(2)} kWh</strong>`;
                    } else {
                        bgClass = 'bg-warning text-dark';
                        message =
                            `<i class="bi bi-exclamation-triangle me-2"></i><strong class="small">Consumption: ${consumption.toFixed(2)} kWh</strong>`;
                    }
                }
            }

            consumptionPreview.className = `${baseClasses} ${bgClass}`;
            consumptionPreview.innerHTML = message;
        }

        ['reading_imported_current', 'reading_imported_calculated', 'reading_produced_current',
            'reading_produced_calculated'
        ].forEach((id) => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updateConsumptionPreview);
                element.addEventListener('change', updateConsumptionPreview);
            }
        });

        function openDisconnectionModal(mode = 'create', button = null) {
            const modalElement = document.getElementById('disconnectionModal');
            const form = document.getElementById('disconnectionForm');
            const methodInput = document.getElementById('disconnectionMethod');
            const submitButton = document.getElementById('disconnectionSubmit');

            if (!modalElement) {
                return;
            }

            form.reset();
            methodInput.value = 'POST';
            form.action = createDisconnectionUrl;
            submitButton.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Event';
            modalElement.querySelector('.modal-title span').textContent = 'Log Disconnection';

            if (mode === 'edit' && button) {
                const dataset = button.dataset;
                methodInput.value = 'PUT';
                form.action = updateDisconnectionUrlTemplate.replace('__disconnection__', dataset.disconnectionId);
                document.getElementById('disconnection_date').value = dataset.disconnectionDate || '';
                document.getElementById('reconnection_date').value = dataset.reconnectionDate || '';
                document.getElementById('disconnection_reason').value = dataset.reason || '';
                submitButton.innerHTML = '<i class="bi bi-save me-1"></i> Update Event';
                modalElement.querySelector('.modal-title span').textContent = 'Edit Disconnection';
            }

            const modal = new boosted.Modal(modalElement);
            modal.show();
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
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

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
