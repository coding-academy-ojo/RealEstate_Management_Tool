@extends('layouts.app')

@section('title', 'Edit Electricity Service')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('electricity-services.index') }}">Electricity Services</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('electricity-services.show', $electricityService) }}">{{ $electricityService->registration_number }}</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-fill me-2 text-orange"></i>
                        Edit Electricity Service Record
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('electricity-services.update', $electricityService) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="building_id" class="form-label fw-bold">
                                Building <span class="text-danger">*</span>
                            </label>
                            <select name="building_id" id="building_id"
                                class="form-select @error('building_id') is-invalid @enderror" required>
                                <option value="">-- Select Building --</option>
                                @foreach ($buildings as $building)
                                    <option value="{{ $building->id }}"
                                        {{ old('building_id', $electricityService->building_id) == $building->id ? 'selected' : '' }}>
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
                                    value="{{ old('subscriber_name', $electricityService->subscriber_name) }}" required>
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
                                    value="{{ old('meter_number', $electricityService->meter_number) }}" required>
                                @error('meter_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Ensure this matches the serial printed on the meter.</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_solar_power" name="has_solar_power"
                                    value="1"
                                    {{ old('has_solar_power', $electricityService->has_solar_power) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="has_solar_power">
                                    Uses Supplemental Solar Power
                                </label>
                            </div>
                            <small class="text-muted">Toggle on if the tenant exports or offsets through solar
                                panels.</small>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-building-gear me-2"></i>Company Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label fw-bold">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="company_name" id="company_name"
                                    class="form-control @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name', $electricityService->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="registration_number" class="form-label fw-bold">
                                    Registration Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="registration_number" id="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number', $electricityService->registration_number) }}"
                                    required>
                                @error('registration_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3 text-orange">
                            <i class="bi bi-chat-left-text me-2"></i>Additional Information
                        </h5>

                        <div class="mb-3">
                            <label for="remarks" class="form-label fw-bold">Remarks (optional)</label>
                            <textarea name="remarks" id="remarks" rows="3" class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Optional notes...">{{ old('remarks', $electricityService->remarks) }}</textarea>
                            <hr class="my-4">

                            <h5 class="mb-3 text-orange">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Documents & Remarks
                            </h5>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="reset_file" class="form-label fw-bold">
                                        Reset File (Optional)
                                    </label>
                                    <input type="file" name="reset_file" id="reset_file"
                                        class="form-control @error('reset_file') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    @error('reset_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Uploading a new file will replace the existing one.</small>
                                    @if ($electricityService->reset_file)
                                        <div class="mt-2">
                                            <a href="{{ route('electricity-services.files.show', [$electricityService, 'reset']) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-text me-1"></i> View current reset file
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="remarks" class="form-label fw-bold">Remarks</label>
                                    <textarea name="remarks" id="remarks" rows="4" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $electricityService->remarks) }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <a href="{{ route('electricity-services.show', $electricityService) }}"
                                    class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-orange">
                                    <i class="bi bi-check-circle me-1"></i> Update Electricity Service
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                    <p class="mb-0">Are you sure you want to change the building for this electricity service record?</p>
                    <p class="text-muted mb-0 mt-2"><small>This will update the building association for this electricity
                            service.</small></p>
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
            const buildingSelect = document.getElementById('building_id');
            const originalBuildingId = '{{ old('building_id', $electricityService->building_id) }}';
            let buildingChoices = null;
            let pendingBuildingId = null;

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

                buildingSelect.addEventListener('change', function(e) {
                    const newBuildingId = this.value;

                    if (newBuildingId && newBuildingId !== originalBuildingId) {
                        e.preventDefault();
                        pendingBuildingId = newBuildingId;

                        const modal = new bootstrap.Modal(document.getElementById('changeBuildingModal'));
                        modal.show();
                    }
                });

                const confirmBtn = document.getElementById('confirmBuildingChange');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function() {
                        const modalElement = document.getElementById('changeBuildingModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                        pendingBuildingId = null;
                    });
                }

                const modalElement = document.getElementById('changeBuildingModal');
                if (modalElement) {
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        if (pendingBuildingId !== null) {
                            buildingChoices.setChoiceByValue(originalBuildingId);
                            pendingBuildingId = null;
                        }
                    });
                }
            }
        });
    </script>
@endpush
