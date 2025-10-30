@extends('layouts.app')

@section('title', 'Land Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('lands.index') }}">Lands</a></li>
    <li class="breadcrumb-item active">{{ $land->code }}</li>
@endsection

@section('content')
    @php
        $currentUser = auth()->user();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <span class="text-orange fw-bold">{{ $land->plot_key }}</span>
            <span class="mx-2">-</span>
            Basin {{ $land->basin }}
        </h2>
        @if ($currentUser?->isSuperAdmin())
            <div>
                <a href="{{ route('lands.edit', $land) }}" class="btn btn-orange">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-danger"
                    onclick="openDeleteModal('{{ $land->id }}', '{{ $land->plot_key }}', 'Basin {{ $land->basin }}')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Land Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2 text-orange"></i>Land Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Plot Key (مفتاح القطعة):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->plot_key }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Site:</strong>
                        </div>
                        <div class="col-md-8">
                            <a href="{{ route('sites.show', $land->site) }}" class="text-decoration-none">
                                {{ $land->site->code }} - {{ $land->site->name }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Governorate (المحافظة):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->governorate ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Region (المنطقة):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->region ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Directorate (المديرية):</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->directorate || $land->directorate_number)
                                {{ $land->directorate ?: '' }}@if ($land->directorate && $land->directorate_number)
                                    ({{ $land->directorate_number }})
                                @elseif($land->directorate_number)
                                    {{ $land->directorate_number }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Village (القرية):</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->village || $land->village_number)
                                {{ $land->village ?: '' }}@if ($land->village && $land->village_number)
                                    ({{ $land->village_number }})
                                @elseif($land->village_number)
                                    {{ $land->village_number }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Basin (الحوض):</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->basin || $land->basin_number)
                                {{ $land->basin ?: '' }}@if ($land->basin && $land->basin_number)
                                    ({{ $land->basin_number }})
                                @elseif($land->basin_number)
                                    {{ $land->basin_number }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Neighborhood (الحي):</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->neighborhood || $land->neighborhood_number)
                                {{ $land->neighborhood ?: '' }}@if ($land->neighborhood && $land->neighborhood_number)
                                    ({{ $land->neighborhood_number }})
                                @elseif($land->neighborhood_number)
                                    {{ $land->neighborhood_number }}
                                @endif
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Plot Number (رقم القطعة):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->plot_number ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Area (المساحة):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ number_format($land->area_m2, 2) }} m²
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Zoning (التنظيم):</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->zoningStatuses->isNotEmpty())
                                @foreach ($land->zoningStatuses as $status)
                                    <span class="badge bg-orange text-white me-1">{{ $status->name_ar }}</span>
                                @endforeach
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Latitude (خط العرض):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->latitude ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Longitude (خط الطول):</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $land->longitude ?: 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Map Location:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->map_location)
                                <a href="{{ $land->map_location }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-geo-alt me-1"></i> View on Map
                                </a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-file-earmark-text me-2 text-orange"></i>Documents
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">سند الملكية:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->ownership_doc)
                                <a href="{{ route('lands.documents.show', [$land, 'ownership']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">مخطط الموقع:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->site_plan)
                                <a href="{{ route('lands.documents.show', [$land, 'site-plan']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">مخطط تنظيمي:</strong>
                        </div>
                        <div class="col-md-8">
                            @if ($land->zoning_plan)
                                <a href="{{ route('lands.documents.show', [$land, 'zoning-plan']) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Buildings & Stats -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-building me-2 text-orange"></i>Quick Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Buildings:</span>
                        <a href="#buildings-section" class="text-decoration-none fw-bold text-success">
                            {{ $land->buildings->count() }}
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Renovations:</span>
                        <span class="fw-bold text-warning">
                            {{ $land->renovations->count() }}
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
                        @if ($currentUser?->isSuperAdmin())
                            <a href="{{ route('buildings.create') }}?site_id={{ $land->site_id }}&land_id={{ $land->id }}"
                                class="btn btn-primary btn-sm">
                                <i class="bi bi-building-fill-add me-1"></i> Add Building
                            </a>
                        @endif

                        @if ($currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('renovation'))
                            <a href="{{ route('renovations.create') }}?innovatable_type=Land&innovatable_id={{ $land->id }}"
                                class="btn btn-warning btn-sm">
                                <i class="bi bi-lightbulb-fill me-1"></i> Add Renovation
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mini Map -->
            @if ($land->latitude && $land->longitude)
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0">
                            <i class="bi bi-geo-alt me-2 text-orange"></i>Location Map
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div id="miniMap" style="height: 300px; width: 100%;"></div>
                    </div>
                    <div class="card-footer bg-light py-2">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Coordinates: {{ number_format($land->latitude, 6) }}, {{ number_format($land->longitude, 6) }}
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Land Images Gallery -->
    @php
        $allImages = [];

        // Add land's own images
foreach ($land->images as $image) {
    $allImages[] = [
        'image' => $image,
        'source' => 'land',
        'source_label' => null,
        'editable' => true,
    ];
}

// Add building images (buildings assigned to this land)
foreach ($land->buildings as $building) {
    foreach ($building->images as $image) {
        $allImages[] = [
            'image' => $image,
            'source' => 'building',
            'source_label' => $building->code,
            'editable' => false,
        ];
    }
}

// Add parent site images
if ($land->site) {
    foreach ($land->site->images as $image) {
        $allImages[] = [
            'image' => $image,
            'source' => 'site',
            'source_label' => $land->site->code,
            'editable' => false,
                ];
            }
        }
    @endphp

    <x-image-gallery :images="$allImages" type="land" :entityId="$land->id" :canEdit="true" :showSourceTags="true"
        title="All Images" />

    <!-- Buildings Section -->
    <div class="card border-0 shadow-sm mb-4" id="buildings-section">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-building me-2 text-orange"></i>Buildings
                <span class="text-muted">({{ $land->buildings->count() }})</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @forelse($land->buildings as $building)
                @if ($loop->first)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Area (m²)</th>
                                    <th>Services</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                @endif
                <tr>
                    <td class="text-orange fw-bold">{{ $building->code }}</td>
                    <td>
                        <div class="fw-semibold">{{ $building->name }}</div>
                        <div class="text-muted small">{{ ucfirst($building->property_type) }}</div>
                    </td>
                    <td>{{ number_format($building->area_m2, 2) }}</td>
                    <td>
                        <small class="text-muted">
                            <i class="bi bi-droplet text-info"></i> {{ $building->waterServices->count() }}
                            <i class="bi bi-lightning text-warning ms-2"></i> {{ $building->electricityServices->count() }}
                        </small>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('buildings.show', $building) }}" class="btn btn-sm btn-outline-primary"
                                title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if ($currentUser?->isSuperAdmin())
                                <a href="{{ route('buildings.edit', $building) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @if ($loop->last)
                    </tbody>
                    </table>
        </div>
        @endif
    @empty
        <p class="text-muted text-center mb-0 py-4">No buildings associated with this land parcel.</p>
        @endforelse
    </div>
    </div>

    <!-- Renovations Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-lightbulb me-2 text-orange"></i>Renovations
                <span class="text-muted">({{ $land->renovations->count() }})</span>
            </h5>
            @if ($currentUser?->isSuperAdmin() || $currentUser?->hasPrivilege('renovation'))
                <a href="{{ route('renovations.create') }}?innovatable_type=Land&innovatable_id={{ $land->id }}"
                    class="btn btn-orange">
                    <i class="bi bi-plus-circle me-1"></i> Add Renovation
                </a>
            @endif
        </div>
        <div class="card-body p-0">
            @forelse($land->renovations as $innovation)
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
                            <a href="{{ route('renovations.show', $innovation) }}"
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
    <p class="text-muted text-center mb-0 py-4">No renovations recorded for this land.</p>
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
                    <p class="mb-2">Are you sure you want to delete this land?</p>
                    <div class="alert alert-warning mb-0">
                        <strong id="deleteLandKey"></strong> - <span id="deleteLandInfo"></span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        <small>This action will move the land to trash. You can restore it later from the Deleted Lands
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

    <!-- Leaflet CSS and JS for Mini Map -->
    @if ($land->latitude && $land->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endif

    <script>
        function openDeleteModal(landId, landKey, landInfo) {
            document.getElementById('deleteLandKey').textContent = landKey;
            document.getElementById('deleteLandInfo').textContent = landInfo;
            document.getElementById('deleteForm').action = '/lands/' + landId;

            // Use Boosted modal API
            const modalElement = document.getElementById('deleteModal');
            const modal = new boosted.Modal(modalElement);
            modal.show();
        }

        @if ($land->latitude && $land->longitude)
            // Initialize mini map
            document.addEventListener('DOMContentLoaded', function() {
                const lat = {{ $land->latitude }};
                const lng = {{ $land->longitude }};

                // Initialize the map
                const miniMap = L.map('miniMap').setView([lat, lng], 15);

                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(miniMap);

                // Add marker for the land location
                const marker = L.marker([lat, lng]).addTo(miniMap);
                marker.bindPopup('<strong>{{ $land->plot_key }}</strong><br>Basin {{ $land->basin }}')
                .openPopup();

                // Add circle to show approximate area if available
                @if ($land->area_m2)
                    const radius = Math.sqrt({{ $land->area_m2 }} / Math.PI);
                    L.circle([lat, lng], {
                        color: '#ff7900',
                        fillColor: '#ff7900',
                        fillOpacity: 0.2,
                        radius: radius
                    }).addTo(miniMap);
                @endif
            });
        @endif
    </script>
@endsection
