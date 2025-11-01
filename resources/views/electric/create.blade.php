@extends('layouts.app')

@section('title', 'Create Electricity Service')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item active">Create</li>
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
        background-image: url("{{ asset('assets/images/energie.png') }}") !important;
        background-repeat: repeat !important;
        background-size: 22px 22px !important;
        opacity: 0.18;
        pointer-events: none;
        z-index: 0;
    }

    #content>* {
        position: relative;
        z-index: 1;
    }

    .d-flex .choices {
        flex-grow: 1;
    }
    .form-check-input {
        border-radius: 10px !important;
    }
</style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-lightning-charge-fill me-2 text-orange"></i>
                        Create New Electricity Service Record
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('electricity-services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="building_id" class="form-label fw-bold">
                                Building <span class="text-danger">*</span>
                            </label>
                            <select name="building_id" id="building_id"
                                class="form-select @error('building_id') is-invalid @enderror" required>
                                <option value="">-- Select Building --</option>
                                @foreach ($buildings as $building)
                                    <option value="{{ $building->id }}"
                                        {{ old('building_id', request('building_id')) == $building->id ? 'selected' : '' }}>
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
                            <i class="bi bi-person-badge me-2"></i>Service Details
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="subscriber_name" class="form-label fw-bold">
                                    Subscriber Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="subscriber_name" id="subscriber_name"
                                    class="form-control @error('subscriber_name') is-invalid @enderror"
                                    value="{{ old('subscriber_name') }}" required>
                                @error('subscriber_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meter_number" class="form-label fw-bold">
                                    Meter Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="meter_number" id="meter_number"
                                    class="form-control @error('meter_number') is-invalid @enderror"
                                    value="{{ old('meter_number') }}" required>
                                @error('meter_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter the unique serial number on the physical meter.</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_solar_power" name="has_solar_power"
                                    value="1" {{ old('has_solar_power') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="has_solar_power">
                                    Uses Supplemental Solar Power
                                </label>
                            </div>
                            <small class="text-muted">Enable if the service relies on solar panels or net-metering.</small>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-building-gear me-2"></i>Company Information
                        </h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="electricity_company_id" class="form-label fw-bold">
                                    Electricity Company <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-2 align-items-start">
                                    <select name="electricity_company_id" id="electricity_company_id"
                                        class="form-select flex-grow-1 @error('electricity_company_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- Select Electricity Company --</option>
                                        @foreach ($electricityCompanies as $company)
                                            <option value="{{ $company->id }}"
                                                data-website="{{ $company->website }}"
                                                data-arabic="{{ $company->name_ar }}"
                                                {{ (string) old('electricity_company_id') === (string) $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}@if ($company->name_ar) — {{ $company->name_ar }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-orange flex-shrink-0" id="addElectricityCompanyBtn">
                                        <i class="bi bi-plus-circle me-1"></i> Add
                                    </button>
                                </div>
                                <div id="electricityCompanyHints" class="form-text mt-1 text-muted"></div>
                                @error('electricity_company_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="registration_number" class="form-label fw-bold">
                                    Registration Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="registration_number" id="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number') }}" required>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-speedometer2 me-2"></i>Reference Readings (Optional)
                        </h5>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold">Remarks (optional)</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Optional notes...">{{ old('remarks') }}</textarea>

                            <hr class="my-4">

                            <h5 class="mb-3 text-orange">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Documents & Remarks
                            </h5>

                            <div class="mb-3">
                                <label for="reset_file" class="form-label fw-bold">
                                    Reset File (Optional)
                                </label>
                                <input type="file" name="reset_file" id="reset_file"
                                    class="form-control @error('reset_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('reset_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Accepted: PDF, JPG, PNG (max 4 MB)</small>
                            </div>

                            <div class="mb-4">
                                <label for="remarks" class="form-label fw-bold">Remarks</label>
                                <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <a href="{{ route('electricity-services.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-orange">
                                    <i class="bi bi-check-circle me-1"></i> Create Electricity Service
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Electricity Company Modal -->
    <div class="modal fade" id="addElectricityCompanyModal" tabindex="-1"
        aria-labelledby="addElectricityCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addElectricityCompanyModalLabel">
                        <i class="bi bi-plus-circle me-2 text-orange"></i>Add Electricity Company
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="electricityCompanyForm">
                        <div class="mb-3">
                            <label for="new_electricity_company_name" class="form-label fw-bold">
                                Company Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="new_electricity_company_name" name="name" class="form-control"
                                required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="new_electricity_company_name_ar" class="form-label fw-bold">
                                Arabic Name
                            </label>
                            <input type="text" id="new_electricity_company_name_ar" name="name_ar" class="form-control"
                                maxlength="255" placeholder="اكتب الاسم بالعربية">
                        </div>
                        <div class="mb-3">
                            <label for="new_electricity_company_website" class="form-label fw-bold">
                                Website
                            </label>
                            <input type="url" id="new_electricity_company_website" name="website" class="form-control"
                                maxlength="255" placeholder="https://example.com">
                            <small class="text-muted">Optional: include https:// for direct linking.</small>
                        </div>
                        <div id="electricityCompanyFormError" class="alert alert-danger d-none mb-0"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="electricityCompanyForm" class="btn btn-orange"
                        id="saveElectricityCompanyBtn">
                        <i class="bi bi-check-circle me-1"></i> Save Company
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
            const electricityCompanySelect = document.getElementById('electricity_company_id');
            const addCompanyBtn = document.getElementById('addElectricityCompanyBtn');
            const addCompanyModalEl = document.getElementById('addElectricityCompanyModal');
            const electricityCompanyForm = document.getElementById('electricityCompanyForm');
            const electricityCompanyFormError = document.getElementById('electricityCompanyFormError');
            const saveElectricityCompanyBtn = document.getElementById('saveElectricityCompanyBtn');
            const companyHintsDisplay = document.getElementById('electricityCompanyHints');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (buildingSelect) {
                new Choices(buildingSelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search by building code or name...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No buildings found',
                    noChoicesText: 'No buildings available',
                    shouldSort: false,
                    removeItemButton: false,
                });
            }

            let electricityCompanyChoices = null;

            const updateCompanyHints = () => {
                if (!electricityCompanySelect || !companyHintsDisplay) {
                    return;
                }

                const selectedOption = electricityCompanySelect.selectedOptions[0];

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

            if (electricityCompanySelect) {
                electricityCompanyChoices = new Choices(electricityCompanySelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search for an electricity company...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No companies found',
                    noChoicesText: 'No companies available',
                    removeItemButton: false,
                    shouldSort: false,
                });

                electricityCompanySelect.addEventListener('change', updateCompanyHints);
                updateCompanyHints();
            }

            let addCompanyModalInstance = getModalInstance(addCompanyModalEl);

            if (addCompanyBtn && addCompanyModalInstance) {
                addCompanyBtn.addEventListener('click', () => {
                    electricityCompanyForm?.reset();
                    if (electricityCompanyFormError) {
                        electricityCompanyFormError.classList.add('d-none');
                        electricityCompanyFormError.textContent = '';
                    }
                    addCompanyModalInstance.show();
                });
            }

            if (electricityCompanyForm) {
                electricityCompanyForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (!csrfToken) {
                        return;
                    }

                    const nameInput = electricityCompanyForm.querySelector('[name="name"]');
                    const nameArabicInput = electricityCompanyForm.querySelector('[name="name_ar"]');
                    const websiteInput = electricityCompanyForm.querySelector('[name="website"]');

                    const payload = {
                        name: nameInput?.value.trim() ?? '',
                        name_ar: nameArabicInput?.value.trim() || null,
                        website: websiteInput?.value.trim() || null,
                    };

                    if (electricityCompanyFormError) {
                        electricityCompanyFormError.classList.add('d-none');
                        electricityCompanyFormError.textContent = '';
                    }

                    if (saveElectricityCompanyBtn) {
                        saveElectricityCompanyBtn.disabled = true;
                        saveElectricityCompanyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                    }

                    try {
                        const response = await fetch('{{ route('electricity-companies.store') }}', {
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
                                ?? 'Unable to save electricity company. Please try again.';
                            throw new Error(errorMessage);
                        }

                        if (electricityCompanyChoices) {
                            const choiceLabel = data.name_ar
                                ? `${data.name} — ${data.name_ar}`
                                : data.name;

                            electricityCompanyChoices.setChoices([
                                {
                                    value: String(data.id),
                                    label: choiceLabel,
                                    selected: true,
                                },
                            ], 'value', 'label', true);

                            const newOption = electricityCompanySelect.querySelector(`option[value="${data.id}"]`);
                            if (newOption) {
                                newOption.dataset.website = data.website ?? '';
                                newOption.dataset.arabic = data.name_ar ?? '';
                            }

                            updateCompanyHints();
                        }

                        addCompanyModalInstance?.hide();
                    } catch (error) {
                        if (electricityCompanyFormError) {
                            electricityCompanyFormError.textContent = error.message ?? 'Unexpected error occurred.';
                            electricityCompanyFormError.classList.remove('d-none');
                        }
                    } finally {
                        if (saveElectricityCompanyBtn) {
                            saveElectricityCompanyBtn.disabled = false;
                            saveElectricityCompanyBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Company';
                        }
                    }
                });
            }
        });
    </script>
@endpush
