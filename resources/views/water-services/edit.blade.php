@extends('layouts.app')

@section('title', 'Edit Water Service')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water.services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('water-services.show', $waterService) }}">{{ $waterService->registration_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('styles')
<style>
    #content {
        background-color: #f8f9fa !important;
        background-image: none !important;
        position: relative;
    }
    #content::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url("{{ asset('assets/images/water-drops.png') }}") !important;
        background-repeat: repeat !important;
        background-size: 20px 20px !important;
        opacity: 0.2;
        pointer-events: none;
        z-index: 0;
    }
    #content > * {
        position: relative;
        z-index: 1;
    }
    .d-flex .choices {
        flex-grow: 1;
    }
</style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-fill me-2 text-orange"></i>
                        Edit Water Service Record
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('water-services.update', $waterService) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Building Selection -->
                        <div class="mb-4">
                            <label for="building_id" class="form-label fw-bold">
                                Building <span class="text-danger">*</span>
                            </label>
                            <select name="building_id" id="building_id"
                                class="form-select @error('building_id') is-invalid @enderror" required>
                                <option value="">-- Select Building --</option>
                                @foreach ($buildings as $building)
                                    <option value="{{ $building->id }}"
                                        {{ old('building_id', $waterService->building_id) == $building->id ? 'selected' : '' }}>
                                        {{ $building->code }} - {{ $building->name }} (Site: {{ $building->site->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('building_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-info-circle me-2"></i>Company Information
                        </h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="water_company_id" class="form-label fw-bold">
                                    Water Company <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2 align-items-start">
                                    <select name="water_company_id" id="water_company_id"
                                        class="form-select flex-grow-1 @error('water_company_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select Water Company --</option>
                                        @foreach ($waterCompanies as $company)
                                            <option value="{{ $company->id }}"
                                                data-website="{{ $company->website }}"
                                                data-arabic="{{ $company->name_ar }}"
                                                {{ (string) old('water_company_id', $waterService->water_company_id) === (string) $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}@if ($company->name_ar) — {{ $company->name_ar }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-orange flex-shrink-0" id="addWaterCompanyBtn">
                                        <i class="bi bi-plus-circle me-1"></i> Add
                                    </button>
                                </div>
                                <div id="waterCompanyHints" class="form-text mt-1 text-muted"></div>
                                @error('water_company_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="registration_number" class="form-label fw-bold">
                                    Registration Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="registration_number" id="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number', $waterService->registration_number) }}" required>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meter_owner_name" class="form-label fw-bold">
                                    Meter Owner Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="meter_owner_name" id="meter_owner_name"
                                    class="form-control @error('meter_owner_name') is-invalid @enderror"
                                    value="{{ old('meter_owner_name', $waterService->meter_owner_name) }}" required>
                                @error('meter_owner_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="iron_number" class="form-label fw-bold">
                                    Iron Number (Water Meter)
                                </label>
                                <input type="text" name="iron_number" id="iron_number"
                                    class="form-control @error('iron_number') is-invalid @enderror"
                                    value="{{ old('iron_number', $waterService->iron_number) }}">
                                @error('iron_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-journal-text me-2"></i>Additional Details
                        </h5>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold">
                                Remarks & Notes
                            </label>
                            <textarea name="remarks" id="remarks" rows="4"
                                class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Add any special instructions or notes for this water service...">{{ old('remarks', $waterService->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="initial_meter_image" class="form-label fw-bold">
                                Reference Image
                            </label>
                            @if ($waterService->initial_meter_image)
                                <div class="mb-2">
                                    <a href="{{ route('water-services.files.show', [$waterService, 'reference-meter']) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View current image
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="initial_meter_image" id="initial_meter_image"
                                class="form-control @error('initial_meter_image') is-invalid @enderror"
                                accept=".jpg,.jpeg,.png">
                            @error('initial_meter_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload a new photo to replace the existing reference image.</small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('water-services.show', $waterService) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-orange">
                                <i class="bi bi-check-circle me-1"></i> Update Water Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Water Company Modal -->
    <div class="modal fade" id="addWaterCompanyModal" tabindex="-1" aria-labelledby="addWaterCompanyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addWaterCompanyModalLabel">
                        <i class="bi bi-plus-circle me-2 text-orange"></i>Add Water Company
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="waterCompanyForm">
                        <div class="mb-3">
                            <label for="new_water_company_name" class="form-label fw-bold">
                                Company Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="new_water_company_name" name="name" class="form-control"
                                required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="new_water_company_name_ar" class="form-label fw-bold">
                                Arabic Name
                            </label>
                            <input type="text" id="new_water_company_name_ar" name="name_ar" class="form-control"
                                maxlength="255" placeholder="اكتب الاسم بالعربية">
                        </div>
                        <div class="mb-3">
                            <label for="new_water_company_website" class="form-label fw-bold">
                                Website
                            </label>
                            <input type="url" id="new_water_company_website" name="website" class="form-control"
                                maxlength="255" placeholder="https://example.com">
                            <small class="text-muted">Optional: include https:// for direct linking.</small>
                        </div>
                        <div id="waterCompanyFormError" class="alert alert-danger d-none mb-0"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="waterCompanyForm" class="btn btn-orange" id="saveWaterCompanyBtn">
                        <i class="bi bi-check-circle me-1"></i> Save Company
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Changing Building -->
    <div class="modal fade" id="changeBuildingModal" tabindex="-1" aria-labelledby="changeBuildingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="changeBuildingModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Building Change
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to change the building for this water service record?</p>
                    <p class="text-muted mb-0 mt-2"><small>This will update the building association for this water service.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmBuildingChange">
                        <i class="bi bi-check-circle me-1"></i> Confirm Change
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const getModalInstance = (element) => {
                if (!element) {
                    return null;
                }
                if (typeof boosted !== 'undefined' && boosted?.Modal) {
                    return boosted.Modal.getOrCreateInstance(element);
                }
                if (typeof bootstrap !== 'undefined' && bootstrap?.Modal) {
                    return bootstrap.Modal.getOrCreateInstance(element);
                }
                return null;
            };

            const buildingSelect = document.getElementById('building_id');
            const changeBuildingModalEl = document.getElementById('changeBuildingModal');
            const originalBuildingId = '{{ old('building_id', $waterService->building_id) }}';
            const waterCompanySelect = document.getElementById('water_company_id');
            const addCompanyBtn = document.getElementById('addWaterCompanyBtn');
            const addCompanyModalEl = document.getElementById('addWaterCompanyModal');
            const waterCompanyForm = document.getElementById('waterCompanyForm');
            const waterCompanyFormError = document.getElementById('waterCompanyFormError');
            const saveWaterCompanyBtn = document.getElementById('saveWaterCompanyBtn');
            const companyHintsDisplay = document.getElementById('waterCompanyHints');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            let buildingChoices = null;
            let pendingBuildingId = null;
            let changeBuildingModalInstance = getModalInstance(changeBuildingModalEl);
            let waterCompanyChoices = null;

            const revertBuildingSelection = () => {
                if (pendingBuildingId !== null) {
                    buildingChoices?.setChoiceByValue(originalBuildingId);
                    pendingBuildingId = null;
                }
            };

            if (buildingSelect) {
                buildingChoices = new Choices(buildingSelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search by building code or name...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No buildings found',
                    noChoicesText: 'No buildings available',
                    shouldSort: false,
                    removeItemButton: false,
                });

                buildingSelect.addEventListener('change', function(event) {
                    const newBuildingId = this.value;

                    if (newBuildingId && newBuildingId !== originalBuildingId) {
                        event.preventDefault();
                        pendingBuildingId = newBuildingId;
                        changeBuildingModalInstance?.show();
                    }
                });

                const confirmBtn = document.getElementById('confirmBuildingChange');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function() {
                        changeBuildingModalInstance?.hide();
                        pendingBuildingId = null;
                    });
                }

                changeBuildingModalEl?.addEventListener('hidden.bs.modal', revertBuildingSelection);
                changeBuildingModalEl?.addEventListener('hidden.boosted.modal', revertBuildingSelection);
            }

            const updateCompanyHints = () => {
                if (!waterCompanySelect || !companyHintsDisplay) {
                    return;
                }

                const selectedOption = waterCompanySelect.selectedOptions[0];

                if (!selectedOption || !selectedOption.value) {
                    companyHintsDisplay.textContent = '';
                    return;
                }

                const arabicName = selectedOption.dataset.arabic;
                const website = selectedOption.dataset.website;
                let hintsHtml = '';

                if (arabicName) {
                    hintsHtml += `<div><i class="bi bi-translate me-1"></i>${arabicName}</div>`;
                }

                if (website) {
                    hintsHtml += `<div><i class="bi bi-globe me-1"></i><a href="${website}" target="_blank" rel="noopener" class="text-decoration-none">${website}</a></div>`;
                }

                companyHintsDisplay.innerHTML = hintsHtml || 'No additional details available.';
            };

            if (waterCompanySelect) {
                waterCompanyChoices = new Choices(waterCompanySelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search for a water company...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No companies found',
                    noChoicesText: 'No companies available',
                    removeItemButton: false,
                    shouldSort: false,
                });

                waterCompanySelect.addEventListener('change', updateCompanyHints);
                updateCompanyHints();
            }

            let addCompanyModalInstance = getModalInstance(addCompanyModalEl);

            if (addCompanyBtn && addCompanyModalInstance) {
                addCompanyBtn.addEventListener('click', () => {
                    waterCompanyForm?.reset();
                    if (waterCompanyFormError) {
                        waterCompanyFormError.classList.add('d-none');
                        waterCompanyFormError.textContent = '';
                    }
                    addCompanyModalInstance.show();
                });
            }

            if (waterCompanyForm) {
                waterCompanyForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (!csrfToken) {
                        return;
                    }

                    const nameInput = waterCompanyForm.querySelector('[name="name"]');
                    const nameArabicInput = waterCompanyForm.querySelector('[name="name_ar"]');
                    const websiteInput = waterCompanyForm.querySelector('[name="website"]');

                    const payload = {
                        name: nameInput?.value.trim() ?? '',
                        name_ar: nameArabicInput?.value.trim() || null,
                        website: websiteInput?.value.trim() || null,
                    };

                    if (waterCompanyFormError) {
                        waterCompanyFormError.classList.add('d-none');
                        waterCompanyFormError.textContent = '';
                    }

                    if (saveWaterCompanyBtn) {
                        saveWaterCompanyBtn.disabled = true;
                        saveWaterCompanyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                    }

                    try {
                        const response = await fetch('{{ route('water.companies.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            const errorMessage = data?.message
                                ?? Object.values(data?.errors ?? {})[0]?.[0]
                                ?? 'Unable to save water company. Please try again.';
                            throw new Error(errorMessage);
                        }

                        if (waterCompanyChoices) {
                            const choiceLabel = data.name_ar
                                ? `${data.name} — ${data.name_ar}`
                                : data.name;

                            waterCompanyChoices.setChoices([
                                {
                                    value: String(data.id),
                                    label: choiceLabel,
                                    selected: true,
                                },
                            ], 'value', 'label', true);

                            const newOption = waterCompanySelect.querySelector(`option[value="${data.id}"]`);
                            if (newOption) {
                                newOption.dataset.website = data.website ?? '';
                                newOption.dataset.arabic = data.name_ar ?? '';
                            }

                            updateCompanyHints();
                        }

                        addCompanyModalInstance?.hide();
                    } catch (error) {
                        if (waterCompanyFormError) {
                            waterCompanyFormError.textContent = error.message ?? 'Unexpected error occurred.';
                            waterCompanyFormError.classList.remove('d-none');
                        }
                    } finally {
                        if (saveWaterCompanyBtn) {
                            saveWaterCompanyBtn.disabled = false;
                            saveWaterCompanyBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Company';
                        }
                    }
                });
            }
        });
    </script>
@endpush
