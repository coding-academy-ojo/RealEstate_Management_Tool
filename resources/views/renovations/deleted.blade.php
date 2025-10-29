@extends('layouts.app')

@section('title', 'Deleted Renovations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('renovations.index') }}">Renovations</a></li>
    <li class="breadcrumb-item active">Deleted Renovations</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Renovations
        </h2>
        <a href="{{ route('renovations.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Renovations
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
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Renovations
                    <span class="badge bg-secondary">{{ $renovations->total() }}</span>
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
                        @forelse ($renovations as $renovation)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ class_basename($renovation->innovatable_type) }}</div>
                                    @if ($renovation->innovatable)
                                        <span class="text-primary">
                                            {{ $renovation->innovatable->name ?? ($renovation->innovatable->code ?? 'N/A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $renovation->date ? $renovation->date->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ number_format($renovation->cost, 2) }}</td>
                                <td>
                                    <span
                                        class="text-muted small">{{ \Illuminate\Support\Str::limit($renovation->description, 50) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $renovation->deleted_at->format('Y-m-d H:i') }}
                                        <br>
                                        <span class="text-warning">{{ $renovation->deleted_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal('{{ $renovation->id }}', '{{ $renovation->name }}', '{{ class_basename($renovation->innovatable_type) }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal('{{ $renovation->id }}', '{{ $renovation->name }}', '{{ class_basename($renovation->innovatable_type) }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-lightbulb" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted renovations found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($renovations->hasPages())
            <div class="card-footer bg-white">
                {{ $renovations->links() }}
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
                    <p class="mb-2">Are you sure you want to restore this renovation?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreRenovationName"></strong> (<span id="restoreRenovationType"></span>)
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This renovation will be moved back to the active list.</small>
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
                    <p class="mb-2">Are you sure you want to permanently delete this renovation?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteRenovationName"></strong> (<span id="forceDeleteRenovationType"></span>)
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the renovation. This action cannot be
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
        function openRestoreModal(renovationId, renovationName, renovationType) {
            document.getElementById('restoreRenovationName').textContent = renovationName;
            document.getElementById('restoreRenovationType').textContent = renovationType;
            document.getElementById('restoreForm').action = '/renovations/' + renovationId + '/restore';

            const modalElement = document.getElementById('restoreModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openForceDeleteModal(renovationId, renovationName, renovationType) {
            document.getElementById('forceDeleteRenovationName').textContent = renovationName;
            document.getElementById('forceDeleteRenovationType').textContent = renovationType;
            document.getElementById('forceDeleteForm').action = '/renovations/' + renovationId + '/force-delete';

            const modalElement = document.getElementById('forceDeleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
