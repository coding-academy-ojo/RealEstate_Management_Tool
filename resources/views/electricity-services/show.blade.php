@extends('layouts.app')

@section('title', 'Electricity Service Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">#{{ $electricityService->id }}</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
            Electricity Service Record
        </h2>
        <div class="btn-group">
            <a href="{{ route('electricity-services.edit', $electricityService) }}" class="btn btn-outline-orange">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-outline-danger"
                onclick="openDeleteModal('{{ $electricityService->id }}', '{{ $electricityService->registration_number }}', '{{ $electricityService->company_name }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2 text-orange"></i>Building Information
                    </h5>
                </div>
                <div class="card-body">
                    @if ($electricityService->building)
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <strong class="text-muted">Building:</strong>
                            </div>
                            <div class="col-md-8">
                                <a href="{{ route('buildings.show', $electricityService->building) }}"
                                    class="text-decoration-none">
                                    <span class="badge bg-orange">{{ $electricityService->building->code }}</span>
                                    {{ $electricityService->building->name }}
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <strong class="text-muted">Site:</strong>
                            </div>
                            <div class="col-md-8">
                                <a href="{{ route('sites.show', $electricityService->building->site) }}"
                                    class="text-decoration-none">
                                    <span class="badge bg-primary">{{ $electricityService->building->site->code }}</span>
                                    {{ $electricityService->building->site->name }}
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Building information not available</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
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
                            {{ $electricityService->company_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Registration Number:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $electricityService->registration_number }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Reading Date:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $electricityService->reading_date ? $electricityService->reading_date->format('F d, Y') : 'N/A' }}
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-orange mb-3">
                        <i class="bi bi-speedometer2 me-2"></i>Meter Readings
                    </h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Previous Reading:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ number_format($electricityService->previous_reading, 2) }} kWh
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Current Reading:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ number_format($electricityService->current_reading, 2) }} kWh
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong class="text-muted">Consumption:</strong>
                        </div>
                        <div class="col-md-8">
                            @php
                                $consumption =
                                    $electricityService->current_reading - $electricityService->previous_reading;
                            @endphp
                            <span class="badge bg-{{ $consumption > 0 ? 'warning' : 'success' }} fs-6">
                                {{ number_format($consumption, 2) }} kWh
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-orange mb-3">
                        <i class="bi bi-file-earmark me-2"></i>Documents & Remarks
                    </h6>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Reset File:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($electricityService->reset_file)
                                <a href="{{ route('electricity-services.files.show', [$electricityService, 'reset']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View File
                                </a>
                            @else
                                <span class="text-muted">No file uploaded</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong class="text-muted">Remarks:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $electricityService->remarks ?: 'No remarks' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Information -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3"
                style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <h6 class="text-white mb-3">
                        <i class="bi bi-lightning-charge me-2"></i>Electricity Consumption
                    </h6>
                    <div class="display-4 fw-bold mb-2">
                        {{ number_format($electricityService->current_reading - $electricityService->previous_reading, 2) }}
                    </div>
                    <div class="text-white-50">Kilowatt Hours (kWh)</div>
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
                        <span class="small">{{ $electricityService->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Updated:</span>
                        <span class="small">{{ $electricityService->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
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
        function openDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('deleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('deleteServiceCompany').textContent = companyName;
            document.getElementById('deleteForm').action = '/electricity-services/' + serviceId;

            // Use Boosted modal API
            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
