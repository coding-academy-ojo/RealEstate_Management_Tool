@extends('layouts.app')

@section('title', 'Site Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
    <li class="breadcrumb-item active">{{ $site->code }}</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <span class="text-orange fw-bold">{{ $site->code }}</span>
            <span class="mx-2">-</span>
            {{ $site->name }}
        </h2>
        <div>
            <a href="{{ route('sites.edit', $site) }}" class="btn btn-orange">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-danger"
                onclick="openDeleteModal('{{ $site->id }}', '{{ $site->code }}', '{{ $site->name }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Site Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-orange"></i>Site Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Code:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->code }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Name:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Cluster Number:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->cluster_no }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Region:</strong>
                        </div>
                        <div class="col-md-8">
                            Region {{ $site->region }} - {{ $site->region_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Governorate:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->governorate_name_en }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Total Area:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ number_format($site->area_m2, 2) }} m²
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Zoning Status:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->zoning_status ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Notes:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $site->notes ?: 'No notes' }}
                        </div>
                    </div>

                    @if ($site->other_documents && count($site->other_documents) > 0)
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong class="text-muted">Other Documents:</strong>
                            </div>
                            <div class="col-md-8">
                                <div class="list-group list-group-flush">
                                    @foreach ($site->other_documents as $doc)
                                        <div class="list-group-item px-0 py-2 d-flex align-items-center">
                                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                            <div class="flex-grow-1">
                                                <strong class="d-block">{{ $doc['name'] ?? 'Document' }}</strong>
                                                <small
                                                    class="text-muted">{{ $doc['original_name'] ?? basename($doc['path'] ?? $doc) }}</small>
                                            </div>
                                            <div class="ms-2">
                                                <a href="{{ asset('storage/' . ($doc['path'] ?? $doc)) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . ($doc['path'] ?? $doc)) }}" download
                                                    class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-orange"></i>Quick Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Buildings:</span>
                        <a href="#buildings-section" class="text-decoration-none fw-bold text-success">
                            {{ $site->buildings->count() }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Lands:</span>
                        <a href="#lands-section" class="text-decoration-none fw-bold text-info">
                            {{ $site->lands->count() }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Re-Innovations:</span>
                        <span class="fw-bold text-warning">
                            {{ $site->reInnovations->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2 text-orange"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('buildings.create') }}?site_id={{ $site->id }}"
                            class="btn btn-orange btn-sm">
                            <i class="bi bi-building-fill-add me-1"></i> Add Building
                        </a>
                        <a href="{{ route('lands.create') }}?site_id={{ $site->id }}" class="btn btn-info btn-sm">
                            <i class="bi bi-map-fill me-1"></i> Add Land
                        </a>
                        <a href="{{ route('re-innovations.create') }}?innovatable_type=Site&innovatable_id={{ $site->id }}"
                            class="btn btn-warning btn-sm">
                            <i class="bi bi-lightbulb-fill me-1"></i> Add Innovation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Site Images Gallery -->
    @php
        $allImages = [];

        // Add site's own images
foreach ($site->images as $image) {
    $allImages[] = [
        'image' => $image,
        'source' => 'site',
        'source_label' => null,
        'editable' => true,
    ];
}

// Add building images
foreach ($site->buildings as $building) {
    foreach ($building->images as $image) {
        $allImages[] = [
            'image' => $image,
            'source' => 'building',
            'source_label' => $building->code,
            'editable' => false,
        ];
    }
}

// Add land images
foreach ($site->lands as $land) {
    foreach ($land->images as $image) {
        $allImages[] = [
            'image' => $image,
            'source' => 'land',
            'source_label' => $land->plot_key,
            'editable' => false,
                ];
            }
        }
    @endphp

    <x-image-gallery :images="$allImages" type="site" :entityId="$site->id" :canEdit="true" :showSourceTags="true"
        title="All Images" />

    <!-- Buildings Section -->
    <div class="card border-0 shadow-sm mb-4" id="buildings-section">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-building me-2 text-orange"></i>Buildings
                <span class="text-muted">({{ $site->buildings->count() }})</span>
            </h5>
            <a href="{{ route('buildings.create') }}?site_id={{ $site->id }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add Building
            </a>
        </div>
        <div class="card-body p-0">
            @forelse($site->buildings as $building)
                @if ($loop->first)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Area (m²)</th>
                                    <th>Permit Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="text-orange fw-bold">{{ $building->code }}</td>
                    <td class="fw-semibold">{{ $building->name }}</td>
                    <td>{{ number_format($building->area_m2, 2) }}</td>
                    <td>
                        @if ($building->has_building_permit)
                            <i class="bi bi-check-circle-fill text-success me-1"></i>Permitted
                        @else
                            <span class="text-muted">No Permit</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('buildings.show', $building) }}" class="btn btn-sm btn-outline-primary"
                                title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @if ($loop->last)
                    </tbody>
                    </table>
        </div>
        @endif
    @empty
        <p class="text-muted text-center mb-0 py-4">No buildings found. Create your first building!</p>
        @endforelse
    </div>
    </div>

    <!-- Lands Section -->
    <div class="card border-0 shadow-sm mb-4" id="lands-section">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-map me-2 text-orange"></i>Lands
                <span class="text-muted">({{ $site->lands->count() }})</span>
            </h5>
            <a href="{{ route('lands.create') }}?site_id={{ $site->id }}" class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add Land
            </a>
        </div>
        <div class="card-body p-0">
            @forelse($site->lands as $land)
                @if ($loop->first)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Parcel Number</th>
                                    <th>Area (m²)</th>
                                    <th>Buildings</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="text-info fw-bold">{{ $land->code }}</td>
                    <td class="fw-semibold">Parcel {{ $land->parcel_no }}</td>
                    <td>{{ number_format($land->area_m2, 2) }}</td>
                    <td>{{ $land->buildings->count() }} Buildings</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('lands.show', $land) }}" class="btn btn-sm btn-outline-primary"
                                title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @if ($loop->last)
                    </tbody>
                    </table>
        </div>
        @endif
    @empty
        <p class="text-muted text-center mb-0 py-4">No lands found. Create your first land parcel!</p>
        @endforelse
    </div>
    </div>

    <!-- Re-Innovations Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-lightbulb me-2 text-orange"></i>Re-Innovations
                <span class="text-muted">({{ $site->reInnovations->count() }})</span>
            </h5>
            <a href="{{ route('re-innovations.create') }}?innovatable_type=Site&innovatable_id={{ $site->id }}"
                class="btn btn-orange">
                <i class="bi bi-plus-circle me-1"></i> Add Innovation
            </a>
        </div>
        <div class="card-body p-0">
            @forelse($site->reInnovations as $innovation)
                @if ($loop->first)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Cost (JOD)</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="fw-semibold">{{ $innovation->name }}</td>
                    <td class="text-success fw-bold">{{ number_format($innovation->cost, 2) }}</td>
                    <td>{{ $innovation->date->format('Y-m-d') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('re-innovations.show', $innovation) }}"
                                class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @if ($loop->last)
                    </tbody>
                    </table>
        </div>
        @endif
    @empty
        <p class="text-muted text-center mb-0 py-4">No re-innovations recorded for this site.</p>
        @endforelse
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
                    <p class="mb-2">Are you sure you want to delete this site?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteSiteCode"></strong> - <span id="deleteSiteName"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the site to trash. You can restore it later from the Deleted Sites
                            page.</small>
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
        function openDeleteModal(siteId, siteCode, siteName) {
            document.getElementById('deleteSiteCode').textContent = siteCode;
            document.getElementById('deleteSiteName').textContent = siteName;
            document.getElementById('deleteForm').action = '/sites/' + siteId;

            // Use Boosted modal API
            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }
    </script>
@endsection
