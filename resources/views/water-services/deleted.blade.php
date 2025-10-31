@extends('layouts.app')

@section('title', 'Deleted Water Services')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water.services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item active">Deleted Services</li>
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
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Water Services
        </h2>
    <a href="{{ route('water.services.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Water Services
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Water Services
                    <span class="badge bg-secondary">{{ $waterServices->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Building</th>
                            <th>Meter Info</th>
                            <th>Latest Reading</th>
                            <th>Latest Bill</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waterServices as $service)
                            @php
                                $companyEnglish = optional($service->waterCompany)->name ?? $service->company_name;
                                $companyArabic = optional($service->waterCompany)->name_ar ?? $service->company_name_ar;
                            @endphp
                            <tr>
                                <td>
                                    @if ($service->building)
                                        <span class="fw-semibold text-primary">{{ $service->building->name }}</span>
                                    @else
                                        <span class="text-muted">No Building</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $service->meter_owner_name }}</div>
                                    <div class="text-muted small">
                                        {{ $companyEnglish }}
                                        @if ($companyArabic)
                                            <span class="d-block">{{ $companyArabic }}</span>
                                        @endif
                                        @if (optional($service->waterCompany)->website)
                                            <a href="{{ $service->waterCompany->website }}" target="_blank"
                                                class="text-decoration-none ms-1">
                                                <i class="bi bi-globe"></i>
                                            </a>
                                        @endif
                                    </div>
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
                                            {{ $latestReading->reading_date?->format('Y-m-d') ?? 'No date' }}
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
                                        <span class="badge rounded-pill fw-semibold {{ $latestReading->is_paid ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                                            {{ $latestReading->is_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="text-muted">No bill</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $service->deleted_at->format('Y-m-d H:i') }}
                                        <br>
                                        <span class="text-warning">{{ $service->deleted_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ addslashes(trim($companyEnglish . ($companyArabic ? ' / ' . $companyArabic : ''))) }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ addslashes(trim($companyEnglish . ($companyArabic ? ' / ' . $companyArabic : ''))) }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-droplet" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted water services found.</p>
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

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="restoreModalLabel">
                        <i class="bi bi-arrow-counterclockwise text-success me-2"></i>Confirm Restore
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to restore this water service?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreServiceRegistration"></strong> - <span id="restoreServiceCompany"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This service will be moved back to the active water services list.</small>
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="restoreForm" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Force Delete Confirmation Modal -->
    <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-labelledby="forceDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title" id="forceDeleteModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Permanent Delete Warning
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2"><strong>⚠️ This action cannot be undone!</strong></p>
                    <p class="mb-2">Are you sure you want to permanently delete this water service?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteServiceRegistration"></strong> - <span
                            id="forceDeleteServiceCompany"></span>
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the service and all associated files.
                            This
                            action cannot be reversed!</small>
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form id="forceDeleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash-fill me-1"></i>Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openRestoreModal(serviceId, registrationNumber, companyName) {
            document.getElementById('restoreServiceRegistration').textContent = registrationNumber;
            document.getElementById('restoreServiceCompany').textContent = companyName;
            document.getElementById('restoreForm').action = '/water-services/' + serviceId + '/restore';

            // Use Boosted modal API
            const modalElement = document.getElementById('restoreModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openForceDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('forceDeleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('forceDeleteServiceCompany').textContent = companyName;
            document.getElementById('forceDeleteForm').action = '/water-services/' + serviceId + '/force-delete';

            // Use Boosted modal API
            const modalElement = document.getElementById('forceDeleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
