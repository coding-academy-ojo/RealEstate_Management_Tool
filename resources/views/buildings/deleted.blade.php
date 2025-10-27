@extends('layouts.app')

@section('title', 'Deleted Buildings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Deleted Buildings</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Buildings
        </h2>
        <a href="{{ route('buildings.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Buildings
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
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Buildings
                    <span class="badge bg-secondary">{{ $buildings->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="deletedBuildingsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">Code</th>
                            <th>Name</th>
                            <th>Site</th>
                            <th>Area (m²)</th>
                            <th>Permits</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buildings as $building)
                            <tr>
                                <td>
                                    <span class="badge bg-orange">{{ $building->code }}</span>
                                </td>
                                <td class="fw-semibold">{{ $building->name }}</td>
                                <td>
                                    @php
                                        $site = $building->site;
                                    @endphp
                                    @if ($site)
                                        <div class="fw-semibold">{{ $site->name }}</div>
                                        <small class="text-muted">{{ $site->code }}</small>
                                        @if ($site->trashed())
                                            <div><span class="badge bg-secondary">Site in trash</span></div>
                                        @endif
                                    @else
                                        <span class="text-muted">Site not available</span>
                                    @endif
                                </td>
                                <td>{{ number_format($building->area_m2 ?? 0, 2) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span
                                            class="badge {{ $building->has_building_permit ? 'bg-success' : 'bg-secondary' }}">
                                            <i
                                                class="bi bi-{{ $building->has_building_permit ? 'check' : 'x' }}-circle me-1"></i>
                                            Build
                                        </span>
                                        <span
                                            class="badge {{ $building->has_occupancy_permit ? 'bg-success' : 'bg-secondary' }}">
                                            <i
                                                class="bi bi-{{ $building->has_occupancy_permit ? 'check' : 'x' }}-circle me-1"></i>
                                            Occupancy
                                        </span>
                                        <span
                                            class="badge {{ $building->has_profession_permit ? 'bg-success' : 'bg-secondary' }}">
                                            <i
                                                class="bi bi-{{ $building->has_profession_permit ? 'check' : 'x' }}-circle me-1"></i>
                                            Profession
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional($building->deleted_at)->format('Y-m-d H:i') }}
                                        <br>
                                        <span
                                            class="text-warning">{{ optional($building->deleted_at)->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal({{ $building->id }}, {{ json_encode($building->code) }}, {{ json_encode($building->name) }})">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal({{ $building->id }}, {{ json_encode($building->code) }}, {{ json_encode($building->name) }})">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted buildings found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($buildings->hasPages())
            <div class="card-footer bg-white">
                {{ $buildings->links() }}
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
                    <p class="mb-2">Are you sure you want to restore this building?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreBuildingCode"></strong>
                        <span class="d-block"><small id="restoreBuildingName" class="text-muted"></small></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This building and its related services will be returned to the active list.</small>
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
                    <p class="mb-2">Are you sure you want to permanently delete this building and all its related data?
                    </p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteBuildingCode"></strong>
                        <span class="d-block"><small id="forceDeleteBuildingName" class="text-white-50"></small></span>
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the building, related services, and
                            files. This action cannot be reversed.</small>
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
        function openRestoreModal(buildingId, buildingCode, buildingName) {
            try {
                document.getElementById('restoreBuildingCode').textContent = buildingCode || '';
                document.getElementById('restoreBuildingName').textContent = buildingName || '';
                document.getElementById('restoreForm').action = '/buildings/' + buildingId + '/restore';

                const modalElement = document.getElementById('restoreModal');
                if (!modalElement) {
                    console.error('Restore modal element not found');
                    return;
                }

                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening restore modal:', error);
                alert('Unable to open the restore confirmation. Please refresh and try again.');
            }
        }

        function openForceDeleteModal(buildingId, buildingCode, buildingName) {
            try {
                document.getElementById('forceDeleteBuildingCode').textContent = buildingCode || '';
                document.getElementById('forceDeleteBuildingName').textContent = buildingName || '';
                document.getElementById('forceDeleteForm').action = '/buildings/' + buildingId + '/force-delete';

                const modalElement = document.getElementById('forceDeleteModal');
                if (!modalElement) {
                    console.error('Force delete modal element not found');
                    return;
                }

                const modal = new boosted.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Error opening force delete modal:', error);
                alert('Unable to open the permanent delete confirmation. Please refresh and try again.');
            }
        }
    </script>
@endsection
