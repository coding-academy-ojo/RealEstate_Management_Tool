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
    $currentPropertyType = old('property_type', $building->property_type);
    $contractStartValue = old('contract_start_date', optional($building->contract_start_date)->format('Y-m-d'));
    $contractEndValue = old('contract_end_date', optional($building->contract_end_date)->format('Y-m-d'));
        $contractValue = old('contract_value', $building->contract_value);
        $annualIncreaseValue = old('annual_increase_rate', $building->annual_increase_rate);
        $increaseEffectiveValue = old('increase_effective_date', optional($building->increase_effective_date)->format('Y-m-d'));
        $specialConditionsValue = old('special_conditions', $building->special_conditions);
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

                        <input type="hidden" name="site_id" id="hidden_site_id" value="{{ old('site_id', $building->site_id) }}">

                        <!-- Site Information -->
                        <div class="mb-4">
                            <label for="site_id_display" class="form-label fw-bold">
                                Site <span class="text-danger">*</span>
                            </label>
                            <select name="site_id_display" id="site_id_display" class="form-select @error('site_id') is-invalid @enderror" required>
                                <option value="">-- Select Site --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}"
                                        {{ old('site_id', $building->site_id) == $site->id ? 'selected' : '' }}
                                        data-code="{{ $site->code }}"
                                        data-name="{{ $site->name }}">
                                        {{ $site->code }} - {{ $site->name }} ({{ $site->governorate_name_en }})
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Changing the site will update the building's location. A confirmation will be requested.
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

                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="property_type" class="form-label fw-bold">
                                    Property Type <span class="text-danger">*</span>
                                </label>
                                <select name="property_type" id="property_type"
                                    class="form-select @error('property_type') is-invalid @enderror" required>
                                    <option value="owned" {{ $currentPropertyType === 'owned' ? 'selected' : '' }}>Owned</option>
                                    <option value="rental" {{ $currentPropertyType === 'rental' ? 'selected' : '' }}>Rental
                                    </option>
                                </select>
                                <small class="text-muted">Switch to rental to manage contract details</small>
                                @error('property_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="rental-fields" class="mt-3"
                            style="display: {{ $currentPropertyType === 'rental' ? 'block' : 'none' }};">
                            <div class="alert alert-warning d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Update contract information for rental buildings.</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="contract_start_date" class="form-label fw-bold">
                                        Contract Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="contract_start_date" id="contract_start_date"
                                        class="form-control @error('contract_start_date') is-invalid @enderror"
                                        value="{{ $contractStartValue }}" data-rental-required="true">
                                    @error('contract_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contract_end_date" class="form-label fw-bold">
                                        Contract End Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="contract_end_date" id="contract_end_date"
                                        class="form-control @error('contract_end_date') is-invalid @enderror"
                                        value="{{ $contractEndValue }}" data-rental-required="true">
                                    @error('contract_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contract_value" class="form-label fw-bold">
                                        Annual Contract Value (JOD) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="contract_value" id="contract_value"
                                        class="form-control @error('contract_value') is-invalid @enderror"
                                        value="{{ $contractValue }}" data-rental-required="true">
                                    @error('contract_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="annual_increase_rate" class="form-label fw-bold">
                                        Annual Increase Rate (%) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="annual_increase_rate"
                                        id="annual_increase_rate"
                                        class="form-control @error('annual_increase_rate') is-invalid @enderror"
                                        value="{{ $annualIncreaseValue }}" data-rental-required="true">
                                    @error('annual_increase_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="increase_effective_date" class="form-label fw-bold">
                                        Increase Effective Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="increase_effective_date" id="increase_effective_date"
                                        class="form-control @error('increase_effective_date') is-invalid @enderror"
                                        value="{{ $increaseEffectiveValue }}" data-rental-required="true">
                                    @error('increase_effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contract_file" class="form-label fw-bold">Contract File</label>
                                    <input type="file" name="contract_file" id="contract_file"
                                        class="form-control @error('contract_file') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted d-block">Accepted: PDF, JPG, PNG, DOC (Max 10MB)</small>
                                    @if ($building->contract_file)
                                        <div class="alert alert-light border mt-2 small">
                                            <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                            Current file:
                                            <a href="{{ route('buildings.files.show', [$building, 'contract-document']) }}"
                                                target="_blank">Download</a>
                                        </div>
                                    @endif
                                    @error('contract_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="special_conditions" class="form-label fw-bold">Special Conditions</label>
                                <textarea name="special_conditions" id="special_conditions" rows="3"
                                    class="form-control @error('special_conditions') is-invalid @enderror">{{ $specialConditionsValue }}</textarea>
                                <small class="text-muted">Include escalation clauses or key contract notes</small>
                                @error('special_conditions')
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
                                        <a href="{{ route('buildings.files.show', [$building, 'building-permit']) }}?download=1"
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
                                        <a href="{{ route('buildings.files.show', [$building, 'occupancy-permit']) }}?download=1"
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
                                        <a href="{{ route('buildings.files.show', [$building, 'profession-permit']) }}?download=1"
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="as_built_drawing_pdf" class="form-label fw-bold">As-Built Drawing (PDF)</label>
                                <input type="file" name="as_built_drawing_pdf" id="as_built_drawing_pdf"
                                    class="form-control @error('as_built_drawing_pdf') is-invalid @enderror" accept=".pdf">
                                <small class="text-muted d-block">Accepted format: PDF (Max: 50MB)</small>
                                @if ($building->as_built_drawing_pdf)
                                    <div class="alert alert-light border mt-2 small">
                                        <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                        Current file:
                                        <a href="{{ route('buildings.files.show', [$building, 'as-built-drawing-pdf']) }}"
                                            target="_blank">Download</a>
                                    </div>
                                @endif
                                @error('as_built_drawing_pdf')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="as_built_drawing_cad" class="form-label fw-bold">As-Built Drawing (AutoCAD)</label>
                                <input type="file" name="as_built_drawing_cad" id="as_built_drawing_cad"
                                    class="form-control @error('as_built_drawing_cad') is-invalid @enderror"
                                    accept=".dwg,.dxf">
                                <small class="text-muted d-block">Accepted formats: DWG, DXF (Max: 50MB)</small>
                                @if ($building->as_built_drawing_cad)
                                    <div class="alert alert-light border mt-2 small">
                                        <i class="bi bi-file-earmark-text me-1 text-orange"></i>
                                        Current file:
                                        <a href="{{ route('buildings.files.show', [$building, 'as-built-drawing-cad']) }}"
                                            target="_blank">Download</a>
                                    </div>
                                @endif
                                @error('as_built_drawing_cad')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
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
            const siteSelect = document.getElementById('site_id_display');
            const landsSection = document.getElementById('lands-section');
            const landsContainer = document.getElementById('lands-container');
            const selectedLandIds = @json($selectedLandIds);
            const propertyTypeSelect = document.getElementById('property_type');
            const rentalFields = document.getElementById('rental-fields');

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

            function toggleRentalFields() {
                if (!propertyTypeSelect || !rentalFields) {
                    return;
                }

                const isRental = propertyTypeSelect.value === 'rental';
                rentalFields.style.display = isRental ? 'block' : 'none';

                const inputs = rentalFields.querySelectorAll('input, textarea, select');
                inputs.forEach((input) => {
                    input.disabled = !isRental;
                    if (input.dataset.rentalRequired === 'true') {
                        input.required = isRental;
                    }

                    if (!isRental && input.type === 'file') {
                        input.value = '';
                    }
                });
            }

            if (propertyTypeSelect) {
                propertyTypeSelect.addEventListener('change', toggleRentalFields);
                toggleRentalFields();
            }

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

            // Initialize Choices.js for searchable site select (before loading initial lands)
            const hiddenSiteInput = document.getElementById('hidden_site_id');
            const originalSiteId = {{ $building->site_id }};
            let siteChoices = null;

            if (siteSelect) {
                siteChoices = new Choices(siteSelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search by site name or code...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No sites found',
                    noChoicesText: 'No sites available',
                    shouldSort: false,
                    removeItemButton: false,
                });

                // Handle site change with confirmation
                siteSelect.addEventListener('addItem', function(e) {
                    const newSiteId = e.detail.value;

                    if (newSiteId && newSiteId != originalSiteId) {
                        // Get site details
                        const selectedOption = siteSelect.querySelector(`option[value="${newSiteId}"]`);
                        const siteName = selectedOption ? selectedOption.dataset.name : '';
                        const siteCode = selectedOption ? selectedOption.dataset.code : '';

                        // Show confirmation modal
                        showSiteChangeModal(newSiteId, siteName, siteCode);
                    } else {
                        // Update hidden input
                        hiddenSiteInput.value = newSiteId;
                        // Load lands for the selected site
                        loadLands(newSiteId);
                    }
                }, false);
            }

            // Load initial lands for the current site (after Choices.js is initialized)
            if (siteSelect && siteSelect.value) {
                loadLands(siteSelect.value);
            }

            // Site change confirmation modal
            function showSiteChangeModal(newSiteId, siteName, siteCode) {
                // Create modal if it doesn't exist
                let modal = document.getElementById('siteChangeModal');
                if (!modal) {
                    const modalHTML = `
                        <div class="modal fade" id="siteChangeModal" tabindex="-1" aria-labelledby="siteChangeModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title" id="siteChangeModalLabel">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Confirm Site Change
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-2"><strong>⚠️ You are about to change the site for this building!</strong></p>
                                        <div class="alert alert-info mb-3">
                                            <strong>New Site:</strong> <span id="newSiteName"></span> (<span id="newSiteCode"></span>)
                                        </div>
                                        <p class="text-muted mb-0">
                                            <small><strong>Note:</strong> This will update the building's location and may affect land associations.</small>
                                        </p>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelSiteChange">Cancel</button>
                                        <button type="button" class="btn btn-warning" id="confirmSiteChange">
                                            <i class="bi bi-check-circle me-1"></i>Confirm Change
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                    modal = document.getElementById('siteChangeModal');
                }

                // Update modal content
                document.getElementById('newSiteName').textContent = siteName;
                document.getElementById('newSiteCode').textContent = siteCode;

                // Show modal
                const bsModal = new boosted.Modal(modal);
                bsModal.show();

                // Handle confirmation
                const confirmBtn = document.getElementById('confirmSiteChange');
                const cancelBtn = document.getElementById('cancelSiteChange');

                // Remove old event listeners
                const newConfirmBtn = confirmBtn.cloneNode(true);
                const newCancelBtn = cancelBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                newConfirmBtn.addEventListener('click', function() {
                    // Update hidden input
                    hiddenSiteInput.value = newSiteId;
                    // Load lands for new site
                    loadLands(newSiteId);
                    bsModal.hide();
                });

                newCancelBtn.addEventListener('click', function() {
                    // Revert to original site
                    if (siteChoices) {
                        siteChoices.setChoiceByValue(originalSiteId.toString());
                    }
                    bsModal.hide();
                });

                // Handle modal close (X button or backdrop)
                modal.addEventListener('hidden.bs.modal', function(e) {
                    // Check if it was closed without confirming
                    if (hiddenSiteInput.value != newSiteId) {
                        if (siteChoices) {
                            siteChoices.setChoiceByValue(originalSiteId.toString());
                        }
                    }
                }, { once: true });
            }
        });
    </script>
@endpush
