@extends('layouts.app')

@section('title', 'Deleted Lands')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('lands.index') }}">Lands</a></li>
    <li class="breadcrumb-item active">Deleted Lands</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Lands
        </h2>
        <a href="{{ route('lands.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Lands
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
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Lands
                    <span class="badge bg-secondary">{{ $lands->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="deletedLandsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Plot Key</th>
                            <th>Basin</th>
                            <th>Site</th>
                            <th>Governorate</th>
                            <th>Area (m²)</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lands as $land)
                            <tr>
                                <td class="fw-semibold">{{ $land->plot_key }}</td>
                                <td>{{ $land->basin ?? 'N/A' }}</td>
                                <td>
                                    @if ($land->site)
                                        {{ $land->site->code }} - {{ $land->site->name }}
                                    @else
                                        <span class="text-muted">Site deleted</span>
                                    @endif
                                </td>
                                <td>{{ $land->governorate ?? 'N/A' }}</td>
                                <td>{{ number_format($land->area_m2, 2) }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($land->deleted_at)->format('Y-m-d H:i') }}
                                        <br>
                                        <span
                                            class="text-warning">{{ optional($land->deleted_at)->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal({{ $land->id }}, '{{ $land->plot_key }}', '{{ $land->basin }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal({{ $land->id }}, '{{ $land->plot_key }}', '{{ $land->basin }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted lands found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($lands->hasPages())
            <div class="card-footer bg-white">
                {{ $lands->links() }}
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
                    <p class="mb-2">Are you sure you want to restore this land?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreLandPlot"></strong>
                        <span class="d-block"><small id="restoreLandInfo" class="text-muted"></small></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This land will be moved back to the active lands list.</small>
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
                    <p class="mb-2">Are you sure you want to permanently delete this land?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteLandPlot"></strong>
                        <span class="d-block"><small id="forceDeleteLandInfo" class="text-white-50"></small></span>
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the land and all associated data. This
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
        function buildInfoText(basin) {
            return basin ? `Basin: ${basin}` : 'No basin information';
        }

        function openRestoreModal(landId, plotKey, basin) {
            try {
                document.getElementById('restoreLandPlot').textContent = plotKey || 'Unknown plot key';
                document.getElementById('restoreLandInfo').textContent = buildInfoText(basin);
                document.getElementById('restoreForm').action = '/lands/' + landId + '/restore';

                const modalElement = document.getElementById('restoreModal');
                if (!modalElement) {
                    console.error('Restore modal element not found');
                    return;
                }

                // Use Boosted modal API
                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening restore modal:', error);
                alert('Error opening modal. Please refresh the page and try again.');
            }
        }

        function openForceDeleteModal(landId, plotKey, basin) {
            try {
                document.getElementById('forceDeleteLandPlot').textContent = plotKey || 'Unknown plot key';
                document.getElementById('forceDeleteLandInfo').textContent = buildInfoText(basin);
                document.getElementById('forceDeleteForm').action = '/lands/' + landId + '/force-delete';

                const modalElement = document.getElementById('forceDeleteModal');
                if (!modalElement) {
                    console.error('Force delete modal element not found');
                    return;
                }

                // Use Boosted modal API
                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening force delete modal:', error);
                alert('Error opening modal. Please refresh the page and try again.');
            }
        }
    </script>
@endsection
