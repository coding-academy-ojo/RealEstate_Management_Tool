@extends('layouts.app')

@section('title', 'Deleted Rennovations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('rennovations.index') }}">Rennovations</a></li>
    <li class="breadcrumb-item active">Deleted Rennovations</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Rennovations
        </h2>
        <a href="{{ route('rennovations.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Rennovations
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
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Rennovations
                    <span class="badge bg-secondary">{{ $rennovations->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type / Related To</th>
                            <th>Date</th>
                            <th>Cost (JOD)</th>
                            <th>Description</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rennovations as $rennovation)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ class_basename($rennovation->innovatable_type) }}</div>
                                    @if ($rennovation->innovatable)
                                        <span class="text-primary">
                                            {{ $rennovation->innovatable->name ?? ($rennovation->innovatable->code ?? 'N/A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $rennovation->date ? $rennovation->date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ number_format($rennovation->cost, 2) }}</td>
                                <td>
                                    <span
                                        class="text-muted small">{{ \Illuminate\Support\Str::limit($rennovation->description, 50) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $rennovation->deleted_at->format('Y-m-d H:i') }}
                                        <br>
                                        <span class="text-warning">{{ $rennovation->deleted_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal('{{ $rennovation->id }}', '{{ $rennovation->name }}', '{{ class_basename($rennovation->innovatable_type) }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal('{{ $rennovation->id }}', '{{ $rennovation->name }}', '{{ class_basename($rennovation->innovatable_type) }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-lightbulb" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted rennovations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($rennovations->hasPages())
            <div class="card-footer bg-white">
                {{ $rennovations->links() }}
            </div>
        @endif
    </div>

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
                    <p class="mb-2">Are you sure you want to restore this rennovation?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreRennovationName"></strong> (<span id="restoreRennovationType"></span>)
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This rennovation will be moved back to the active list.</small>
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
                    <p class="mb-2">Are you sure you want to permanently delete this rennovation?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteRennovationName"></strong> (<span id="forceDeleteRennovationType"></span>)
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the rennovation. This action cannot be
                            reversed!</small>
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
        function openRestoreModal(rennovationId, rennovationName, rennovationType) {
            document.getElementById('restoreRennovationName').textContent = rennovationName;
            document.getElementById('restoreRennovationType').textContent = rennovationType;
            document.getElementById('restoreForm').action = '/rennovations/' + rennovationId + '/restore';

            const modalElement = document.getElementById('restoreModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openForceDeleteModal(rennovationId, rennovationName, rennovationType) {
            document.getElementById('forceDeleteRennovationName').textContent = rennovationName;
            document.getElementById('forceDeleteRennovationType').textContent = rennovationType;
            document.getElementById('forceDeleteForm').action = '/rennovations/' + rennovationId + '/force-delete';

            const modalElement = document.getElementById('forceDeleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
