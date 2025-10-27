@extends('layouts.app')

@section('title', 'Edit Building')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Edit {{ $building->code }}</li>
@endsection

@section('content')
    @php
        $selectedLandIds = old('lands', $selectedLandIds ?? []);
        if (!is_array($selectedLandIds)) {
            $selectedLandIds = [];
        }
        $selectedLandIds = array_map('intval', $selectedLandIds);
        $hasBuildingPermit = old('has_building_permit', $building->has_building_permit);
        $hasOccupancyPermit = old('has_occupancy_permit', $building->has_occupancy_permit);
        $hasProfessionPermit = old('has_profession_permit', $building->has_profession_permit);
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-building-gear me-2 text-orange"></i>
                        Edit Building
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('buildings.update', $building) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="site_id" value="{{ $building->site_id }}">

                        <!-- Site Information -->
                        <div class="mb-4">
                            <label for="site_id" class="form-label fw-bold">
                                Site <span class="text-danger">*</span>
                            </label>
                            <select id="site_id" class="form-select" disabled>
                                <option value="{{ $building->site_id }}" selected>
                                    {{ $building->site->code }} - {{ $building->site->name }}
                                    ({{ $building->site->governorate_name_en }})
                                </option>
                            </select>
                            @error('site_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-info d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Site cannot be changed. Create a new building if it belongs to another site.
                            </small>
                        </div>

                        <!-- Lands Selection -->
                        <div class="mb-4" id="lands-section">
                            <label for="lands" class="form-label fw-bold">
                                Lands (Optional)
                            </label>
                            <div id="lands-container" class="border rounded p-3 bg-light">
                                <div class="text-center py-2 text-muted">
                                    <div class="spinner-border spinner-border-sm text-orange me-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    Loading lands...
                                </div>
                            </div>
                            @error('lands')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select the land parcels associated with this building. Leave empty to
                                keep it assigned to the entire site.</small>
                        </div>

                        <hr class="my-4">

                        <!-- Building Information -->
                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Building Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Building Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $building->name) }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="area_m2" class="form-label fw-bold">Area (m²) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="area_m2" id="area_m2"
                                    value="{{ old('area_m2', $building->area_m2) }}"
                                    class="form-control @error('area_m2') is-invalid @enderror" required>
                                @error('area_m2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permits -->
                        <h5 class="mb-3 text-orange mt-4">
                            <i class="bi bi-file-earmark-check me-2"></i>Permits & Documents
                        </h5>

                        <!-- Building Permit -->
                        <div class="mb-4">
                            <div class="toggle-switch-wrapper mb-3">
                                <input type="checkbox" name="has_building_permit" id="has_building_permit"
                                    class="toggle-input" value="1" {{ $hasBuildingPermit ? 'checked' : '' }}>
                                <label for="has_building_permit" class="toggle-label">
                                    <span class="toggle-text">رخصة بناء (Building Permit)</span>
                                </label>
                            </div>
                            <div id="building_permit_upload" style="display: {{ $hasBuildingPermit ? 'block' : 'none' }};">
                                <label for="building_permit_file" class="form-label">Upload Building Permit File</label>
                                <input type="file" name="building_permit_file" id="building_permit_file"
                                    class="form-control @error('building_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @if ($building->building_permit_file)
                                    <div class="alert alert-light border mt-2 small">
                                        <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                        Current file:
                                        <a href="{{ asset('storage/' . $building->building_permit_file) }}"
                                            target="_blank">Download</a>
                                    </div>
                                @endif
                                @error('building_permit_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Occupancy Permit -->
                        <div class="mb-4">
                            <div class="toggle-switch-wrapper mb-3">
                                <input type="checkbox" name="has_occupancy_permit" id="has_occupancy_permit"
                                    class="toggle-input" value="1" {{ $hasOccupancyPermit ? 'checked' : '' }}>
                                <label for="has_occupancy_permit" class="toggle-label">
                                    <span class="toggle-text">إذن إشغال (Occupancy Permit)</span>
                                </label>
                            </div>
                            <div id="occupancy_permit_upload"
                                style="display: {{ $hasOccupancyPermit ? 'block' : 'none' }};">
                                <label for="occupancy_permit_file" class="form-label">Upload Occupancy Permit File</label>
                                <input type="file" name="occupancy_permit_file" id="occupancy_permit_file"
                                    class="form-control @error('occupancy_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @if ($building->occupancy_permit_file)
                                    <div class="alert alert-light border mt-2 small">
                                        <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                        Current file:
                                        <a href="{{ asset('storage/' . $building->occupancy_permit_file) }}"
                                            target="_blank">Download</a>
                                    </div>
                                @endif
                                @error('occupancy_permit_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profession Permit -->
                        <div class="mb-4">
                            <div class="toggle-switch-wrapper mb-3">
                                <input type="checkbox" name="has_profession_permit" id="has_profession_permit"
                                    class="toggle-input" value="1" {{ $hasProfessionPermit ? 'checked' : '' }}>
                                <label for="has_profession_permit" class="toggle-label">
                                    <span class="toggle-text">رخصة مهن (Profession Permit)</span>
                                </label>
                            </div>
                            <div id="profession_permit_upload"
                                style="display: {{ $hasProfessionPermit ? 'block' : 'none' }};">
                                <label for="profession_permit_file" class="form-label">Upload Profession Permit
                                    File</label>
                                <input type="file" name="profession_permit_file" id="profession_permit_file"
                                    class="form-control @error('profession_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted d-block">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @if ($building->profession_permit_file)
                                    <div class="alert alert-light border mt-2 small">
                                        <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                        Current file:
                                        <a href="{{ asset('storage/' . $building->profession_permit_file) }}"
                                            target="_blank">Download</a>
                                    </div>
                                @endif
                                @error('profession_permit_file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- As-Built Drawing -->
                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-file-earmark-ruled me-2"></i>Technical Documents
                        </h5>

                        <div class="mb-3">
                            <label for="as_built_drawing" class="form-label fw-bold">As-Built Drawing (مخطط
                                التنفيذ)</label>
                            <input type="file" name="as_built_drawing" id="as_built_drawing"
                                class="form-control @error('as_built_drawing') is-invalid @enderror" accept=".pdf,.dwg">
                            <small class="text-muted d-block">Accepted formats: PDF, DWG (AutoCAD) (Max: 50MB)</small>
                            @if ($building->as_built_drawing)
                                <div class="alert alert-light border mt-2 small">
                                    <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                    Current file:
                                    <a href="{{ asset('storage/' . $building->as_built_drawing) }}"
                                        target="_blank">Download</a>
                                </div>
                            @endif
                            @error('as_built_drawing')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $building->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('buildings.show', $building) }}" class="btn btn-light">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const siteSelect = document.getElementById('site_id');
            const landsSection = document.getElementById('lands-section');
            const landsContainer = document.getElementById('lands-container');
            const selectedLandIds = @json($selectedLandIds);

            const buildingPermitCheck = document.getElementById('has_building_permit');
            const buildingPermitUpload = document.getElementById('building_permit_upload');
            const buildingPermitFile = document.getElementById('building_permit_file');

            const occupancyPermitCheck = document.getElementById('has_occupancy_permit');
            const occupancyPermitUpload = document.getElementById('occupancy_permit_upload');
            const occupancyPermitFile = document.getElementById('occupancy_permit_file');

            const professionPermitCheck = document.getElementById('has_profession_permit');
            const professionPermitUpload = document.getElementById('profession_permit_upload');
            const professionPermitFile = document.getElementById('profession_permit_file');

            function toggleSection(checkbox, section, fileInput) {
                if (!checkbox || !section) {
                    return;
                }

                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                        if (fileInput) {
                            fileInput.value = '';
                        }
                    }
                });
            }

            toggleSection(buildingPermitCheck, buildingPermitUpload, buildingPermitFile);
            toggleSection(occupancyPermitCheck, occupancyPermitUpload, occupancyPermitFile);
            toggleSection(professionPermitCheck, professionPermitUpload, professionPermitFile);

            function renderLands(lands) {
                if (!lands || lands.length === 0) {
                    landsContainer.innerHTML = `
                        <p class="text-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            No lands available for this site.
                        </p>
                    `;
                    return;
                }

                let html = '<div class="row">';
                lands.forEach(land => {
                    const isChecked = selectedLandIds.includes(Number(land.id));
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="lands[]" value="${land.id}"
                                    id="land_${land.id}" ${isChecked ? 'checked' : ''}>
                                <label class="form-check-label" for="land_${land.id}">
                                    <strong>Plot ${land.plot_number || 'N/A'}</strong> - Basin ${land.basin || 'N/A'}
                                    <span class="text-muted ms-1">(${land.village || 'N/A'})</span>
                                </label>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                landsContainer.innerHTML = html;
            }

            function loadLands(siteId) {
                if (!siteId) {
                    landsContainer.innerHTML = `
                        <p class="text-muted mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Select a site to view available lands.
                        </p>
                    `;
                    return;
                }

                landsContainer.innerHTML = `
                    <div class="text-center py-2 text-muted">
                        <div class="spinner-border spinner-border-sm text-orange me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading lands...
                    </div>
                `;

                fetch(`/api/sites/${siteId}/lands`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(renderLands)
                    .catch(error => {
                        console.error('Error fetching lands:', error);
                        landsContainer.innerHTML = `
                            <p class="text-danger mb-0">
                                <i class="bi bi-x-circle me-1"></i>
                                Failed to load lands. Please try again later.
                            </p>
                        `;
                    });
            }

            if (siteSelect && siteSelect.value) {
                loadLands(siteSelect.value);
            }
        });
    </script>
@endpush
