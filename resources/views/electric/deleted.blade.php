@extends('layouts.app')

@section('title', 'Deleted Electricity Services')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Deleted Services</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Electricity Services
        </h2>
        <a href="{{ route('electricity-services.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Electricity Services
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
            <h5 class="mb-0">
                <i class="bi bi-archive me-2 text-muted"></i>Trashed Electricity Services
                <span class="badge bg-secondary">{{ $electricityServices->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Subscriber</th>
                            <th>Meter</th>
                            <th>Building</th>
                            <th>Company</th>
                            <th>Registration #</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($electricityServices as $service)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $service->subscriber_name }}</div>
                                    <small class="text-muted">
                                        Solar:
                                        @if ($service->has_solar_power)
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-dark text-white">{{ $service->meter_number }}</span>
                                </td>
                                <td>
                                    @if ($service->building)
                                        <span class="fw-semibold text-primary">{{ $service->building->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $service->building->code }}</small>
                                    @else
                                        <span class="text-muted">No Building</span>
                                    @endif
                                </td>
                                <td>{{ $service->company_name }}</td>
                                <td>{{ $service->registration_number }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $service->deleted_at->format('Y-m-d H:i') }}
                                        <br>
                                        <span class="text-warning">{{ $service->deleted_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ $service->company_name }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Permanently"
                                            onclick="openForceDeleteModal('{{ $service->id }}', '{{ $service->registration_number }}', '{{ $service->company_name }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-lightning-charge" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted electricity services found.</p>
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

    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 bg-success text-white">
                    <h5 class="modal-title" id="restoreModalLabel">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Restore Electricity Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to restore this electricity service?</p>
                    <div class="alert alert-success mb-0">
                        <strong id="restoreServiceRegistration"></strong> - <span id="restoreServiceCompany"></span>
                    </div>
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
                    <p class="mb-2"><strong>This action cannot be undone!</strong></p>
                    <p class="mb-2">Are you sure you want to permanently delete this electricity service?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteServiceRegistration"></strong> - <span id="forceDeleteServiceCompany"></span>
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the service and all associated files.</small>
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
            document.getElementById('restoreForm').action = '/electricity-services/' + serviceId + '/restore';

            const modalElement = document.getElementById('restoreModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openForceDeleteModal(serviceId, registrationNumber, companyName) {
            document.getElementById('forceDeleteServiceRegistration').textContent = registrationNumber;
            document.getElementById('forceDeleteServiceCompany').textContent = companyName;
            document.getElementById('forceDeleteForm').action = '/electricity-services/' + serviceId + '/force-delete';

            const modalElement = document.getElementById('forceDeleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
