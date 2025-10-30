@extends('layouts.app')

@section('title', 'Edit Water Service')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('water-services.index') }}">Water Services</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('water-services.show', $waterService) }}">{{ $waterService->registration_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
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
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label fw-bold">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="company_name" id="company_name"
                                    class="form-control @error('company_name') is-invalid @enderror"
                                    value="{{ old('company_name', $waterService->company_name) }}" required>
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
            const buildingSelect = document.getElementById('building_id');
            const originalBuildingId = '{{ old('building_id', $waterService->building_id) }}';
            let buildingChoices = null;
            let pendingBuildingId = null;

            if (buildingSelect) {
                // Initialize Choices.js for searchable building dropdown
                buildingChoices = new Choices(buildingSelect, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Search by building code or name...',
                    itemSelectText: 'Press to select',
                    noResultsText: 'No buildings found',
                    noChoicesText: 'No buildings available',
                    shouldSort: false,
                    removeItemButton: false,
                });

                // Listen for building changes
                buildingSelect.addEventListener('change', function(e) {
                    const newBuildingId = this.value;

                    // If building changed from original, show confirmation modal
                    if (newBuildingId && newBuildingId !== originalBuildingId) {
                        e.preventDefault();
                        pendingBuildingId = newBuildingId;

                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('changeBuildingModal'));
                        modal.show();
                    }
                });

                // Handle confirmation
                const confirmBtn = document.getElementById('confirmBuildingChange');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function() {
                        // Close modal
                        const modalElement = document.getElementById('changeBuildingModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }

                        // Keep the new selection
                        pendingBuildingId = null;
                    });
                }

                // Handle modal cancel/close - revert to original
                const modalElement = document.getElementById('changeBuildingModal');
                if (modalElement) {
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        // If user canceled, revert to original building
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
