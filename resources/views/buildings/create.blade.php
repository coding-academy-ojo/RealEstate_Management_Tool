@extends('layouts.app')

@section('title', 'Create Building')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('buildings.index') }}">Buildings</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    @php
        $oldTenure = old('tenure_type', 'owned');
    @endphp
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-building-fill-add me-2 text-orange"></i>
                        Create New Building
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('buildings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Site Selection -->
                        <div class="mb-4">
                            <label for="site_id" class="form-label fw-bold">
                                Site <span class="text-danger">*</span>
                            </label>
                            <select name="site_id" id="site_id" class="form-select @error('site_id') is-invalid @enderror"
                                required {{ request('site_id') ? 'disabled' : '' }}>
                                <option value="">-- Select Site --</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}"
                                        {{ (old('site_id') ?? request('site_id')) == $site->id ? 'selected' : '' }}>
                                        {{ $site->code }} - {{ $site->name }} ({{ $site->governorate_name_en }})
                                    </option>
                                @endforeach
                            </select>
                            @if (request('site_id'))
                                <input type="hidden" name="site_id" value="{{ request('site_id') }}">
                                <small class="text-info">
                                    <i class="bi bi-lock-fill me-1"></i>Site is locked (coming from site page)
                                </small>
                            @else
                                <small class="text-muted">Select the site where this building will be located</small>
                            @endif
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lands Selection (will be populated via JavaScript) -->
                        <div class="mb-4" id="lands-section" style="display: none;">
                            <label for="lands" class="form-label fw-bold">
                                Lands (Optional)
                            </label>
                            <div id="lands-container" class="border rounded p-3 bg-light">
                                <p class="text-muted mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Select a site first to see available lands
                                </p>
                            </div>
                            @error('lands')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Optionally select land parcels for this building, or leave empty to
                                assign directly to site</small>
                        </div>

                        <hr class="my-4">

                        <!-- Building Information -->
                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Building Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Building Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="area_m2" class="form-label fw-bold">
                                    Area (m²) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="area_m2" id="area_m2"
                                    class="form-control @error('area_m2') is-invalid @enderror"
                                    value="{{ old('area_m2') }}" required>
                                @error('area_m2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-4 mb-3">
                                <label for="tenure_type" class="form-label fw-bold">
                                    Tenure Type <span class="text-danger">*</span>
                                </label>
                                <select name="tenure_type" id="tenure_type"
                                    class="form-select @error('tenure_type') is-invalid @enderror" required>
                                    <option value="owned" {{ $oldTenure === 'owned' ? 'selected' : '' }}>Owned</option>
                                    <option value="rental" {{ $oldTenure === 'rental' ? 'selected' : '' }}>Rental</option>
                                </select>
                                <small class="text-muted">Choose how this building is held</small>
                                @error('tenure_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div id="rental-fields" class="mt-3"
                            style="display: {{ $oldTenure === 'rental' ? 'block' : 'none' }};">
                            <div class="alert alert-warning d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Provide lease information for rental buildings.</span>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="lease_start_date" class="form-label fw-bold">
                                        Lease Start Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="lease_start_date" id="lease_start_date"
                                        class="form-control @error('lease_start_date') is-invalid @enderror"
                                        value="{{ old('lease_start_date') }}" data-rental-required="true">
                                    @error('lease_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="lease_end_date" class="form-label fw-bold">
                                        Lease End Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="lease_end_date" id="lease_end_date"
                                        class="form-control @error('lease_end_date') is-invalid @enderror"
                                        value="{{ old('lease_end_date') }}" data-rental-required="true">
                                    @error('lease_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contract_value" class="form-label fw-bold">
                                        Annual Contract Value (JOD) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="contract_value" id="contract_value"
                                        class="form-control @error('contract_value') is-invalid @enderror"
                                        value="{{ old('contract_value') }}" data-rental-required="true">
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
                                        value="{{ old('annual_increase_rate') }}" data-rental-required="true">
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
                                        value="{{ old('increase_effective_date') }}" data-rental-required="true">
                                    @error('increase_effective_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="contract_file" class="form-label fw-bold">
                                        Lease Contract File
                                    </label>
                                    <input type="file" name="contract_file" id="contract_file"
                                        class="form-control @error('contract_file') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted d-block">Accepted: PDF, JPG, PNG, DOC (Max 10MB)</small>
                                    @error('contract_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="special_conditions" class="form-label fw-bold">Special Conditions</label>
                                <textarea name="special_conditions" id="special_conditions" rows="3"
                                    class="form-control @error('special_conditions') is-invalid @enderror">{{ old('special_conditions') }}</textarea>
                                <small class="text-muted">Add escalation clauses or special terms if any</small>
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
                                    class="toggle-input" value="1" {{ old('has_building_permit') ? 'checked' : '' }}>
                                <label for="has_building_permit" class="toggle-label">
                                    <span class="toggle-text">رخصة بناء (Building Permit)</span>
                                </label>
                            </div>
                            <div id="building_permit_upload"
                                style="display: {{ old('has_building_permit') ? 'block' : 'none' }};">
                                <label for="building_permit_file" class="form-label">
                                    Upload Building Permit File
                                </label>
                                <input type="file" name="building_permit_file" id="building_permit_file"
                                    class="form-control @error('building_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @error('building_permit_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Occupancy Permit -->
                        <!-- Occupancy Permit -->
                        <div class="mb-4">
                            <div class="toggle-switch-wrapper mb-3">
                                <input type="checkbox" name="has_occupancy_permit" id="has_occupancy_permit"
                                    class="toggle-input" value="1" {{ old('has_occupancy_permit') ? 'checked' : '' }}>
                                <label for="has_occupancy_permit" class="toggle-label">
                                    <span class="toggle-text">إذن إشغال (Occupancy Permit)</span>
                                </label>
                            </div>
                            <div id="occupancy_permit_upload"
                                style="display: {{ old('has_occupancy_permit') ? 'block' : 'none' }};">
                                <label for="occupancy_permit_file" class="form-label">
                                    Upload Occupancy Permit File
                                </label>
                                <input type="file" name="occupancy_permit_file" id="occupancy_permit_file"
                                    class="form-control @error('occupancy_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @error('occupancy_permit_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Profession Permit -->
                        <!-- Profession Permit -->
                        <div class="mb-4">
                            <div class="toggle-switch-wrapper mb-3">
                                <input type="checkbox" name="has_profession_permit" id="has_profession_permit"
                                    class="toggle-input" value="1"
                                    {{ old('has_profession_permit') ? 'checked' : '' }}>
                                <label for="has_profession_permit" class="toggle-label">
                                    <span class="toggle-text">رخصة مهن (Profession Permit)</span>
                                </label>
                            </div>
                            <div id="profession_permit_upload"
                                style="display: {{ old('has_profession_permit') ? 'block' : 'none' }};">
                                <label for="profession_permit_file" class="form-label">
                                    Upload Profession Permit File
                                </label>
                                <input type="file" name="profession_permit_file" id="profession_permit_file"
                                    class="form-control @error('profession_permit_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG (Max: 10MB)</small>
                                @error('profession_permit_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- As-Built Drawing -->
                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-file-earmark-ruled me-2"></i>Technical Documents
                        </h5>

                        <div class="mb-3">
                            <label for="as_built_drawing" class="form-label fw-bold">
                                As-Built Drawing (مخطط التنفيذ)
                            </label>
                            <input type="file" name="as_built_drawing" id="as_built_drawing"
                                class="form-control @error('as_built_drawing') is-invalid @enderror" accept=".pdf,.dwg">
                            <small class="text-muted">Accepted formats: PDF, DWG (AutoCAD) (Max: 50MB)</small>
                            @error('as_built_drawing')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('buildings.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Create Building
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Building create page loaded');

                const siteSelect = document.getElementById('site_id');
                const landsSection = document.getElementById('lands-section');
                const landsContainer = document.getElementById('lands-container');
                const tenureSelect = document.getElementById('tenure_type');
                const rentalFields = document.getElementById('rental-fields');

                // Permit toggle handlers
                const buildingPermitCheck = document.getElementById('has_building_permit');
                const buildingPermitUpload = document.getElementById('building_permit_upload');
                const buildingPermitFile = document.getElementById('building_permit_file');

                const occupancyPermitCheck = document.getElementById('has_occupancy_permit');
                const occupancyPermitUpload = document.getElementById('occupancy_permit_upload');
                const occupancyPermitFile = document.getElementById('occupancy_permit_file');

                const professionPermitCheck = document.getElementById('has_profession_permit');
                const professionPermitUpload = document.getElementById('profession_permit_upload');
                const professionPermitFile = document.getElementById('profession_permit_file');

                // Building Permit toggle
                if (buildingPermitCheck) {
                    buildingPermitCheck.addEventListener('change', function() {
                        if (this.checked) {
                            buildingPermitUpload.style.display = 'block';
                        } else {
                            buildingPermitUpload.style.display = 'none';
                            buildingPermitFile.value = '';
                        }
                    });
                }

                // Occupancy Permit toggle
                if (occupancyPermitCheck) {
                    occupancyPermitCheck.addEventListener('change', function() {
                        if (this.checked) {
                            occupancyPermitUpload.style.display = 'block';
                        } else {
                            occupancyPermitUpload.style.display = 'none';
                            occupancyPermitFile.value = '';
                        }
                    });
                }

                // Profession Permit toggle
                if (professionPermitCheck) {
                    professionPermitCheck.addEventListener('change', function() {
                        if (this.checked) {
                            professionPermitUpload.style.display = 'block';
                        } else {
                            professionPermitUpload.style.display = 'none';
                            professionPermitFile.value = '';
                        }
                    });
                }

                const toggleRentalFields = () => {
                    if (!tenureSelect || !rentalFields) {
                        return;
                    }

                    const isRental = tenureSelect.value === 'rental';
                    rentalFields.style.display = isRental ? 'block' : 'none';

                    const inputs = rentalFields.querySelectorAll('input, textarea, select');
                    inputs.forEach((input) => {
                        input.disabled = !isRental;

                        if (!isRental && input.type === 'file') {
                            input.value = '';
                        }

                        if (input.dataset.rentalRequired === 'true') {
                            input.required = isRental;
                        }
                    });
                };

                if (tenureSelect) {
                    tenureSelect.addEventListener('change', toggleRentalFields);
                    toggleRentalFields();
                }

                console.log('Elements found:', {
                    siteSelect: !!siteSelect,
                    landsSection: !!landsSection,
                    landsContainer: !!landsContainer,
                    tenureSelect: !!tenureSelect,
                });

                if (!siteSelect || !landsSection || !landsContainer) {
                    console.error('Required elements not found!');
                    return;
                }

                siteSelect.addEventListener('change', function() {
                    const siteId = this.value;

                    console.log('Site selected:', siteId);

                    if (!siteId) {
                        landsSection.style.display = 'none';
                        landsContainer.innerHTML = `
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Select a site first to see available lands
                            </p>
                        `;
                        return;
                    }

                    // Show loading state
                    landsContainer.innerHTML = `
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-orange me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span>Loading lands...</span>
                        </div>
                    `;
                    landsSection.style.display = 'block';
                    console.log('Showing lands section, fetching from:', `/api/sites/${siteId}/lands`);

                    // Fetch lands for the selected site
                    fetch(`/api/sites/${siteId}/lands`)
                        .then(response => {
                            console.log('Response received:', response.status, response.statusText);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(lands => {
                            console.log('Lands data:', lands);
                            console.log('Number of lands:', lands.length);

                            if (!lands || lands.length === 0) {
                                landsContainer.innerHTML = `
                                    <p class="text-warning mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        No lands available for this site. Please create lands first.
                                    </p>
                                `;
                                landsSection.style.display = 'block';
                                return;
                            }

                            let html = '<div class="row">';
                            const preselectedLandId = '{{ request('land_id') }}';
                            lands.forEach(land => {
                                console.log('Processing land:', land);
                                const isChecked = preselectedLandId && land.id ==
                                    preselectedLandId ? 'checked' : '';
                                html += `
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="lands[]"
                                                value="${land.id}" id="land_${land.id}" ${isChecked}>
                                            <label class="form-check-label" for="land_${land.id}">
                                                <strong>Plot ${land.plot_number || 'N/A'}</strong> - Basin ${land.basin || 'N/A'}
                                                <span class="text-muted ms-1">(${land.village || 'N/A'})</span>
                                            </label>
                                        </div>
                                    </div>
                                `;
                            });
                            html += '</div>';

                            if (preselectedLandId) {
                                html += `
                                    <small class="text-info d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Land is pre-selected (coming from land page). You can select additional lands if needed.
                                    </small>
                                `;
                            }

                            console.log('Setting HTML for lands');
                            landsContainer.innerHTML = html;
                            landsSection.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error fetching lands:', error);
                            landsContainer.innerHTML = `
                                <p class="text-danger mb-0">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Error loading lands: ${error.message}
                                </p>
                            `;
                            landsSection.style.display = 'block';
                        });
                });

                // Trigger change if there's an old site_id value
                if (siteSelect.value) {
                    console.log('Triggering change for pre-selected site:', siteSelect.value);
                    siteSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
