@extends('layouts.app')

@section('title', 'Deleted Sites')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
    <li class="breadcrumb-item active">Deleted Sites</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-trash me-2 text-muted"></i>Deleted Sites
        </h2>
        <a href="{{ route('sites.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Sites
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
                    <i class="bi bi-archive me-2 text-muted"></i>Trashed Sites
                    <span class="badge bg-secondary">{{ $sites->total() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="deletedSitesTable">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Governorate</th>
                            <th>Area (m²)</th>
                            <th>Buildings</th>
                            <th>Lands</th>
                            <th>Deleted At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sites as $site)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $site->code }}</span>
                                </td>
                                <td class="fw-semibold">{{ $site->name }}</td>
                                <td>
                                    {{ $site->governorate_name_en }}
                                    <br><small class="text-muted">{{ $site->region_name }}</small>
                                </td>
                                <td>{{ number_format($site->area_m2, 2) }}</td>
                                <td>{{ $site->buildings->count() }}</td>
                                <td>{{ $site->lands->count() }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $site->deleted_at->format('Y-m-d H:i') }}
                                        <br>
                                        <span class="text-warning">{{ $site->deleted_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-success" title="Restore"
                                            onclick="openRestoreModal('{{ $site->id }}', '{{ $site->code }}', '{{ $site->name }}')">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Delete Permanently"
                                            onclick="openForceDeleteModal('{{ $site->id }}', '{{ $site->code }}', '{{ $site->name }}')">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">No deleted sites found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($sites->hasPages())
            <div class="card-footer bg-white">
                {{ $sites->links() }}
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
                    <p class="mb-2">Are you sure you want to restore this site?</p>
                    <div class="alert alert-info mb-0">
                        <strong id="restoreSiteCode"></strong> - <span id="restoreSiteName"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This site will be moved back to the active sites list.</small>
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
                    <p class="mb-2">Are you sure you want to permanently delete this site?</p>
                    <div class="alert alert-danger mb-0">
                        <strong id="forceDeleteSiteCode"></strong> - <span id="forceDeleteSiteName"></span>
                    </div>
                    <p class="text-danger mt-2 mb-0">
                        <small><strong>Warning:</strong> This will permanently delete the site and all associated data. This
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
        function openRestoreModal(siteId, siteCode, siteName) {
            document.getElementById('restoreSiteCode').textContent = siteCode;
            document.getElementById('restoreSiteName').textContent = siteName;
            document.getElementById('restoreForm').action = '/sites/' + siteId + '/restore';

            // Use Boosted modal API
            const modalElement = document.getElementById('restoreModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        function openForceDeleteModal(siteId, siteCode, siteName) {
            document.getElementById('forceDeleteSiteCode').textContent = siteCode;
            document.getElementById('forceDeleteSiteName').textContent = siteName;
            document.getElementById('forceDeleteForm').action = '/sites/' + siteId + '/force-delete';

            // Use Boosted modal API
            const modalElement = document.getElementById('forceDeleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
